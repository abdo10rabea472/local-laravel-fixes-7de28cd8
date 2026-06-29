<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Language;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

/**
 * Professional XML Sitemap Generator
 * - Multilingual (hreflang via xhtml:link) for every active language
 * - Auto sitemap index when URLs > 50,000
 * - gzip variants (.xml.gz) for any sitemap file
 * - Cached for performance (auto-invalidated by ?fresh=1 or admin save)
 * - Honors sitemap_enabled & seo_indexing_enabled site settings
 */
class SitemapController extends Controller
{
    /** Google's hard limit per sitemap file */
    private const MAX_URLS_PER_FILE = 50000;

    /** Cache TTL in seconds (1 hour) */
    private const CACHE_TTL = 3600;

    // ─────────────────────────────────────────────────────────────────────────
    //  Public endpoints
    // ─────────────────────────────────────────────────────────────────────────

    /** /sitemap.xml — sitemap index OR single urlset depending on URL count */
    public function index(Request $request): Response
    {
        $entries = $this->collectEntries();
        $chunks  = array_chunk($entries, self::MAX_URLS_PER_FILE);

        // Single sitemap if it fits
        if (count($chunks) <= 1) {
            return $this->respondXml($this->cacheRemember('sitemap:single', function () use ($chunks) {
                return $this->buildUrlset($chunks[0] ?? []);
            }, $request));
        }

        // Sitemap index pointing to chunked files
        $xml = $this->cacheRemember('sitemap:index:'.count($chunks), function () use ($chunks) {
            $now  = now()->toAtomString();
            $out  = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
            $out .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
            foreach (array_keys($chunks) as $i) {
                $loc = url('/sitemap-'.($i + 1).'.xml');
                $out .= "  <sitemap>\n    <loc>".htmlspecialchars($loc, ENT_XML1)."</loc>\n    <lastmod>{$now}</lastmod>\n  </sitemap>\n";
            }
            $out .= '</sitemapindex>';
            return $out;
        }, $request);

        return $this->respondXml($xml);
    }

    /** /sitemap-{n}.xml — one chunk of the sitemap index */
    public function chunk(Request $request, int $n): Response
    {
        $entries = $this->collectEntries();
        $chunks  = array_chunk($entries, self::MAX_URLS_PER_FILE);
        $chunk   = $chunks[$n - 1] ?? null;
        abort_unless($chunk, 404);

        $xml = $this->cacheRemember('sitemap:chunk:'.$n, fn () => $this->buildUrlset($chunk), $request);
        return $this->respondXml($xml);
    }

    /** /sitemap.xml.gz — gzip-compressed version of the main sitemap */
    public function gzip(Request $request): Response
    {
        $xml      = $this->index($request)->getContent();
        $gzipped  = gzencode($xml, 9);
        return response($gzipped, 200, [
            'Content-Type'     => 'application/gzip',
            'Content-Encoding' => 'gzip',
            'Cache-Control'    => 'public, max-age='.self::CACHE_TTL,
        ]);
    }

    /** /sitemap-{n}.xml.gz — gzip a specific chunk */
    public function chunkGzip(Request $request, int $n): Response
    {
        $xml = $this->chunk($request, $n)->getContent();
        return response(gzencode($xml, 9), 200, [
            'Content-Type'     => 'application/gzip',
            'Content-Encoding' => 'gzip',
            'Cache-Control'    => 'public, max-age='.self::CACHE_TTL,
        ]);
    }

    /** /robots.txt — dynamic, follows Google spec */
    public function robots(): Response
    {
        $body = trim((string) site_setting('robots_txt_content', ''));

        if ($body === '') {
            $disallow = [
                '/admin/', '/login', '/register', '/password/',
                '/dashboard', '/account', '/cart', '/checkout',
                '/storage/framework/', '/vendor/', '/build/',
                '/api/', '/livewire/',
            ];

            // If global indexing is disabled, block everything
            if ((string) site_setting('seo_indexing_enabled', '1') !== '1') {
                $body = "User-agent: *\nDisallow: /";
            } else {
                $body  = "User-agent: *\n";
                foreach ($disallow as $p) {
                    $body .= "Disallow: {$p}\n";
                }
                $body .= "Allow: /\n";
            }
        }

        // Always advertise the sitemap (well-known location)
        if (!preg_match('/^\s*Sitemap:/mi', $body)) {
            $body .= "\n\nSitemap: ".url('/sitemap.xml');
        }

        return response($body."\n", 200, [
            'Content-Type'  => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Cache invalidation hook
    // ─────────────────────────────────────────────────────────────────────────

    /** Call from a model observer or admin save to flush sitemap cache */
    public static function flushCache(): void
    {
        foreach (Cache::get('sitemap:keys', []) as $key) {
            Cache::forget($key);
        }
        Cache::forget('sitemap:keys');
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Internals
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Collect every public URL as a "translation group":
     *   ['path' => '/products', 'lastmod' => '…', 'changefreq' => 'daily', 'priority' => '0.9']
     * One entry => one <url> with xhtml:link alternates for every active locale.
     */
    private function collectEntries(): array
    {
        // If the user disabled the sitemap or indexing entirely, return nothing
        if ((string) site_setting('sitemap_enabled', '1') !== '1') return [];
        if ((string) site_setting('seo_indexing_enabled', '1') !== '1') return [];

        $now = now()->toAtomString();
        $out = [];

        $push = function (string $path, ?string $lastmod = null, string $cf = 'weekly', string $pr = '0.7') use (&$out, $now) {
            // Normalize: must start with /, no trailing slash except root
            $path = '/'.ltrim($path, '/');
            if (strlen($path) > 1) $path = rtrim($path, '/');
            $out[$path] = ['path' => $path, 'lastmod' => $lastmod ?: $now, 'changefreq' => $cf, 'priority' => $pr];
        };

        // ── Static pages ────────────────────────────────────────────────────
        $push('/',                 $now, 'daily',   '1.0');
        $this->safeRoute($push, 'products.index',  '/products',       'daily',   '0.9');
        $this->safeRoute($push, 'blog.index',      '/blog',           'daily',   '0.7');
        $this->safeRoute($push, 'pages.faqs',      '/faqs',           'monthly', '0.5');
        $this->safeRoute($push, 'pages.privacy',   '/privacy-policy', 'yearly',  '0.3');
        $this->safeRoute($push, 'pages.returns',   '/returns-refunds','yearly',  '0.3');

        // ── Products (only active, not deleted) ─────────────────────────────
        if (Schema::hasTable('products')) {
            Product::query()
                ->when(Schema::hasColumn('products', 'status'),     fn ($q) => $q->where('status', 1))
                ->when(Schema::hasColumn('products', 'is_active'),  fn ($q) => $q->where('is_active', 1))
                ->when(Schema::hasColumn('products', 'deleted_at'), fn ($q) => $q->whereNull('deleted_at'))
                ->select(['slug', 'updated_at'])->orderByDesc('updated_at')
                ->chunk(1000, function ($rows) use ($push) {
                    foreach ($rows as $p) {
                        if ($p->slug) $push('/product/'.$p->slug, optional($p->updated_at)?->toAtomString(), 'weekly', '0.8');
                    }
                });
        }

        // ── Categories ──────────────────────────────────────────────────────
        if (Schema::hasTable('categories')) {
            Category::query()
                ->when(Schema::hasColumn('categories', 'is_active'),  fn ($q) => $q->where('is_active', 1))
                ->when(Schema::hasColumn('categories', 'deleted_at'), fn ($q) => $q->whereNull('deleted_at'))
                ->select(['slug', 'updated_at'])->orderByDesc('updated_at')
                ->chunk(1000, function ($rows) use ($push) {
                    foreach ($rows as $c) {
                        if ($c->slug) $push('/category/'.$c->slug, optional($c->updated_at)?->toAtomString(), 'weekly', '0.7');
                    }
                });
        }

        // ── Blog posts (only published) ─────────────────────────────────────
        if (Schema::hasTable('blog_posts')) {
            BlogPost::query()
                ->when(Schema::hasColumn('blog_posts', 'published_at'), fn ($q) => $q->whereNotNull('published_at')->where('published_at', '<=', now()))
                ->when(Schema::hasColumn('blog_posts', 'status'),       fn ($q) => $q->where('status', 'published'))
                ->when(Schema::hasColumn('blog_posts', 'deleted_at'),   fn ($q) => $q->whereNull('deleted_at'))
                ->select(['slug', 'updated_at'])->orderByDesc('updated_at')
                ->chunk(1000, function ($rows) use ($push) {
                    foreach ($rows as $b) {
                        if ($b->slug) $push('/blog/'.$b->slug, optional($b->updated_at)?->toAtomString(), 'weekly', '0.6');
                    }
                });
        }

        // ── Custom static pages (CMS) ───────────────────────────────────────
        if (Schema::hasTable('pages')) {
            Page::query()
                ->when(Schema::hasColumn('pages', 'is_active'),  fn ($q) => $q->where('is_active', 1))
                ->when(Schema::hasColumn('pages', 'status'),     fn ($q) => $q->where('status', 'published'))
                ->when(Schema::hasColumn('pages', 'deleted_at'), fn ($q) => $q->whereNull('deleted_at'))
                ->select(['slug', 'updated_at'])->limit(2000)
                ->get()->each(function ($p) use ($push) {
                    if ($p->slug) $push('/p/'.$p->slug, optional($p->updated_at)?->toAtomString(), 'monthly', '0.5');
                });
        }

        return array_values($out);
    }

    /** Build a `<urlset>` XML doc with hreflang alternates per locale */
    private function buildUrlset(array $entries): string
    {
        $locales = $this->locales();
        $default = $this->defaultLocale($locales);
        $base    = $this->baseHost();

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" '
              . 'xmlns:xhtml="http://www.w3.org/1999/xhtml">'."\n";

        foreach ($entries as $e) {
            foreach ($locales as $loc) {
                $xml .= "  <url>\n";
                $xml .= "    <loc>".htmlspecialchars($base.'/'.$loc.($e['path'] === '/' ? '' : $e['path']), ENT_XML1)."</loc>\n";
                $xml .= "    <lastmod>{$e['lastmod']}</lastmod>\n";
                $xml .= "    <changefreq>{$e['changefreq']}</changefreq>\n";
                $xml .= "    <priority>{$e['priority']}</priority>\n";
                // hreflang alternates for every other locale + x-default
                foreach ($locales as $alt) {
                    $altUrl = $base.'/'.$alt.($e['path'] === '/' ? '' : $e['path']);
                    $xml .= '    <xhtml:link rel="alternate" hreflang="'.$alt.'" href="'.htmlspecialchars($altUrl, ENT_XML1).'"/>'."\n";
                }
                $xml .= '    <xhtml:link rel="alternate" hreflang="x-default" href="'.htmlspecialchars($base.'/'.$default.($e['path'] === '/' ? '' : $e['path']), ENT_XML1).'"/>'."\n";
                $xml .= "  </url>\n";
            }
        }

        $xml .= '</urlset>';
        return $xml;
    }

    /** Active locale codes (falls back to app locale if Languages table is empty) */
    private function locales(): array
    {
        return Cache::remember('sitemap:locales', 600, function () {
            $codes = [];
            if (Schema::hasTable('languages')) {
                $q = Language::query();
                if (Schema::hasColumn('languages', 'is_active')) $q->where('is_active', 1);
                $codes = $q->orderBy('id')->pluck('code')->filter()->values()->all();
            }
            return $codes ?: [config('app.locale', 'en')];
        });
    }

    private function defaultLocale(array $locales): string
    {
        $app = config('app.locale', 'en');
        return in_array($app, $locales, true) ? $app : ($locales[0] ?? 'en');
    }

    /** Scheme+host WITHOUT any /{locale} path forced by middleware */
    private function baseHost(): string
    {
        $url = config('app.url') ?: url('/');
        $p   = parse_url($url);
        $scheme = $p['scheme'] ?? request()->getScheme();
        $host   = $p['host']   ?? request()->getHost();
        $port   = isset($p['port']) ? ':'.$p['port'] : '';
        return $scheme.'://'.$host.$port;
    }

    private function safeRoute(callable $push, string $name, string $fallback, string $cf, string $pr): void
    {
        try {
            $url  = route($name);
            $path = parse_url($url, PHP_URL_PATH) ?: $fallback;
            // Strip any leading /{locale}
            $path = preg_replace('#^/('.implode('|', $this->locales()).')(?=/|$)#', '', $path) ?: '/';
            $push($path, null, $cf, $pr);
        } catch (\Throwable $e) {
            $push($fallback, null, $cf, $pr);
        }
    }

    private function respondXml(string $xml): Response
    {
        return response($xml, 200, [
            'Content-Type'  => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age='.self::CACHE_TTL,
        ]);
    }

    private function cacheRemember(string $key, \Closure $cb, Request $request): string
    {
        if ($request->boolean('fresh')) Cache::forget($key);

        // Track key for flushCache()
        $keys = Cache::get('sitemap:keys', []);
        if (!in_array($key, $keys, true)) {
            $keys[] = $key;
            Cache::put('sitemap:keys', $keys, self::CACHE_TTL);
        }

        return Cache::remember($key, self::CACHE_TTL, $cb);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  IndexNow + key file (unchanged behaviour)
    // ─────────────────────────────────────────────────────────────────────────

    public function pingGoogle()
    {
        $sitemap = url('/sitemap.xml');
        $host    = parse_url($sitemap, PHP_URL_HOST);

        if (in_array($host, ['localhost', '127.0.0.1', '0.0.0.0']) || str_ends_with((string) $host, '.local')) {
            return response()->json([
                'ok'    => false,
                'error' => 'الموقع يعمل محلياً. محركات البحث لا تستطيع الوصول إلى '.$host.'. انشر الموقع على دومين عام أولاً.',
            ]);
        }

        $key = site_setting('indexnow_key');
        if (!$key) {
            $key = bin2hex(random_bytes(16));
            \App\Models\SiteSetting::updateOrCreate(['key' => 'indexnow_key'], ['value' => $key]);
        }

        $urls = [$sitemap];
        try {
            $xml = @simplexml_load_string(Http::timeout(10)->get($sitemap)->body());
            if ($xml) {
                $xml->registerXPathNamespace('s', 'http://www.sitemaps.org/schemas/sitemap/0.9');
                foreach ($xml->xpath('//s:url/s:loc') ?: [] as $loc) {
                    $urls[] = (string) $loc;
                }
            }
        } catch (\Throwable $e) {}
        $urls = array_values(array_unique(array_slice($urls, 0, 10000)));

        $results = [];
        try {
            $res = Http::timeout(15)->post('https://api.indexnow.org/IndexNow', [
                'host'        => $host,
                'key'         => $key,
                'keyLocation' => url('/'.$key.'.txt'),
                'urlList'     => $urls,
            ]);
            $results['indexnow'] = ['status' => $res->status(), 'ok' => $res->successful()];
        } catch (\Throwable $e) {
            $results['indexnow'] = ['ok' => false, 'error' => $e->getMessage()];
        }

        return response()->json([
            'ok'      => $results['indexnow']['ok'] ?? false,
            'urls'    => count($urls),
            'key_url' => url('/'.$key.'.txt'),
            'results' => $results,
            'note'    => 'تم إرسال الـ URLs إلى IndexNow (Bing/Yandex). جوجل أوقف ping endpoint؛ للفهرسة على جوجل سجّل الموقع في Google Search Console مرة واحدة وسيقرأ sitemap.xml تلقائياً.',
        ]);
    }

    public function indexnowKey(string $key)
    {
        $stored = site_setting('indexnow_key');
        if (!$stored || $stored !== $key) abort(404);
        return response($stored, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
