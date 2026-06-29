<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [];
        $now = now()->toAtomString();

        $add = function (string $loc, ?string $lastmod = null, string $changefreq = 'weekly', string $priority = '0.7') use (&$urls) {
            $urls[] = compact('loc','lastmod','changefreq','priority');
        };

        // الروابط الأساسية فقط
        $add(url('/'), $now, 'daily', '1.0');
        $add(route('products.index'), $now, 'daily', '0.9');
        $add(route('blog.index'), $now, 'daily', '0.7');
        $add(route('pages.faqs'), $now, 'monthly', '0.5');
        $add(route('pages.privacy'), $now, 'monthly', '0.5');
        $add(route('pages.returns'), $now, 'monthly', '0.5');

        // المنتجات
        if (\Schema::hasTable('products')) {
            Product::query()
                ->when(\Schema::hasColumn('products','status'), fn($q) => $q->where('status', 1))
                ->select(['slug','updated_at'])->orderByDesc('updated_at')->limit(5000)
                ->get()->each(function ($p) use ($add) {
                    if ($p->slug) $add(route('product.show', $p->slug), optional($p->updated_at)?->toAtomString(), 'weekly', '0.8');
                });
        }

        // التصنيفات
        if (\Schema::hasTable('categories')) {
            Category::query()->select(['slug','updated_at'])->orderByDesc('updated_at')->limit(2000)
                ->get()->each(function ($c) use ($add) {
                    if ($c->slug) $add(route('category.show', $c->slug), optional($c->updated_at)?->toAtomString(), 'weekly', '0.7');
                });
        }

        // مقالات المدونة (المنشورة فقط)
        if (\Schema::hasTable('blog_posts')) {
            BlogPost::published()
                ->select(['slug','updated_at'])->orderByDesc('updated_at')->limit(5000)
                ->get()->each(function ($b) use ($add) {
                    if ($b->slug) $add(route('blog.show', $b->slug), optional($b->updated_at)?->toAtomString(), 'weekly', '0.6');
                });
        }

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>".htmlspecialchars($u['loc'], ENT_XML1)."</loc>\n";
            if (!empty($u['lastmod'])) $xml .= "    <lastmod>{$u['lastmod']}</lastmod>\n";
            $xml .= "    <changefreq>{$u['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$u['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function robots(): Response
    {
        $custom = trim((string) site_setting('robots_txt_content', ''));
        if ($custom === '') {
            $custom = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /account\nDisallow: /cart\nDisallow: /checkout";
        }

        // إلحاق Allow صريح لكل صفحات الموقع المأخوذة من sitemap
        $allowLines = [];
        try {
            $xml = @simplexml_load_string($this->index()->getContent());
            if ($xml) {
                $base = rtrim(url('/'), '/');
                foreach ($xml->url as $u) {
                    $path = parse_url((string) $u->loc, PHP_URL_PATH) ?: '/';
                    $allowLines[$path] = "Allow: {$path}";
                }
            }
        } catch (\Throwable $e) {}

        if ($allowLines) {
            $custom .= "\n\n# جميع صفحات الموقع\n".implode("\n", $allowLines);
        }

        $custom .= "\n\nSitemap: ".url('/sitemap.xml')."\n";
        return response($custom, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }


    public function pingGoogle()
    {
        $this->useCanonicalRoot();
        $sitemap = request()->getSchemeAndHttpHost().'/sitemap.xml';
        $host    = parse_url($sitemap, PHP_URL_HOST);

        // الموقع لازم يكون publicly accessible (مش localhost/127.0.0.1)
        if (in_array($host, ['localhost', '127.0.0.1', '0.0.0.0']) || str_ends_with((string)$host, '.local')) {
            return response()->json([
                'ok'      => false,
                'error'   => 'الموقع يعمل محلياً. محركات البحث لا تستطيع الوصول إلى '.$host.'. انشر الموقع على دومين عام أولاً.',
            ]);
        }

        // مفتاح IndexNow ثابت لكل تثبيت (يُحفظ تلقائياً)
        $key = site_setting('indexnow_key');
        if (!$key) {
            $key = bin2hex(random_bytes(16));
            \App\Models\SiteSetting::updateOrCreate(['key' => 'indexnow_key'], ['value' => $key]);
        }

        // اجمع كل URLs من sitemap.xml لإرسالها
        $urls = [$sitemap];
        try {
            $xml = @simplexml_load_string(Http::timeout(10)->get($sitemap)->body());
            if ($xml) {
                foreach ($xml->url as $u) {
                    $urls[] = (string) $u->loc;
                }
            }
        } catch (\Throwable $e) {}
        $urls = array_values(array_unique(array_slice($urls, 0, 10000)));

        $results = [];
        try {
            // IndexNow — يصل Bing و Yandex و Seznam دفعة واحدة
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

    // ملف مفتاح IndexNow — يجب أن يكون متاحاً على /{key}.txt
    public function indexnowKey(string $key)
    {
        $stored = site_setting('indexnow_key');
        if (!$stored || $stored !== $key) {
            abort(404);
        }
        return response($stored, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}

