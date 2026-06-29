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
        $entries = [];
        $now = now()->toAtomString();

        // path هنا بدون بادئة اللغة (نضيفها لاحقًا لكل لغة)
        $add = function (string $path, ?string $lastmod = null, string $changefreq = 'weekly', string $priority = '0.7') use (&$entries) {
            $path = '/' . ltrim(parse_url($path, PHP_URL_PATH) ?? '/', '/');
            $entries[$path] = compact('path','lastmod','changefreq','priority');
        };

        $statics = [
            ['home',                  'daily',   '1.0'],
            ['products.index',        'daily',   '0.9'],
            ['offers',                'daily',   '0.8'],
            ['blog.index',            'daily',   '0.7'],
            ['about',                 'monthly', '0.6'],
            ['contact',               'monthly', '0.5'],
            ['track-order',           'monthly', '0.5'],
            ['pages.faqs',            'monthly', '0.5'],
            ['pages.privacy',         'yearly',  '0.3'],
            ['pages.returns',         'yearly',  '0.3'],
        ];
        foreach ($statics as [$name, $freq, $pri]) {
            try { $add(route($name, [], false), $now, $freq, $pri); } catch (\Throwable $e) {}
        }

        $reserved = ['about','faqs','privacy-policy','returns-refunds','payment-success','checkout','contact','blog','offers'];
        if (\Schema::hasTable('pages')) {
            Page::query()
                ->when(\Schema::hasColumn('pages','is_active'), fn($q) => $q->where('is_active', 1))
                ->whereNotIn('slug', $reserved)
                ->select(['slug','updated_at'])->limit(500)
                ->get()->each(function ($p) use ($add) {
                    if ($p->slug) $add(route('pages.show', $p->slug, false), optional($p->updated_at)?->toAtomString(), 'monthly', '0.5');
                });
        }

        if (\Schema::hasTable('products')) {
            Product::query()
                ->when(\Schema::hasColumn('products','status'), fn($q) => $q->where('status', 1))
                ->select(['slug','updated_at'])->orderByDesc('updated_at')->limit(5000)
                ->get()->each(function ($p) use ($add) {
                    if ($p->slug) $add(route('product.show', $p->slug, false), optional($p->updated_at)?->toAtomString(), 'weekly', '0.8');
                });
        }

        if (\Schema::hasTable('categories')) {
            Category::query()->select(['slug','updated_at'])->orderByDesc('updated_at')->limit(2000)
                ->get()->each(function ($c) use ($add) {
                    if ($c->slug) $add(route('category.show', $c->slug, false), optional($c->updated_at)?->toAtomString(), 'weekly', '0.7');
                });
        }

        if (\Schema::hasTable('blog_posts')) {
            BlogPost::published()
                ->select(['slug','updated_at'])->orderByDesc('updated_at')->limit(5000)
                ->get()->each(function ($b) use ($add) {
                    if ($b->slug) $add(route('blog.show', $b->slug, false), optional($b->updated_at)?->toAtomString(), 'weekly', '0.6');
                });
        }

        // اللغات النشطة + الافتراضية لإنشاء hreflang alternates
        $langs = app(\App\Services\LanguageService::class);
        $codes = $langs->codes();
        if (empty($codes)) $codes = [config('app.locale', 'en')];
        $default = optional($langs->default())->code ?? $codes[0];
        $base = rtrim(config('app.url'), '/');

        $localized = function (string $code, string $path) use ($base) {
            $path = $path === '/' ? '' : $path;
            return $base . '/' . $code . $path;
        };

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'."\n"
              . '        xmlns:xhtml="http://www.w3.org/1999/xhtml"'."\n"
              . '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'."\n"
              . '        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 https://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">'."\n";

        foreach ($entries as $e) {
            foreach ($codes as $code) {
                $loc = $localized($code, $e['path']);
                $xml .= "  <url>\n";
                $xml .= "    <loc>".htmlspecialchars($loc, ENT_XML1)."</loc>\n";
                if (!empty($e['lastmod'])) $xml .= "    <lastmod>{$e['lastmod']}</lastmod>\n";
                $xml .= "    <changefreq>{$e['changefreq']}</changefreq>\n";
                $xml .= "    <priority>{$e['priority']}</priority>\n";
                foreach ($codes as $alt) {
                    $xml .= '    <xhtml:link rel="alternate" hreflang="'.$alt.'" href="'.htmlspecialchars($localized($alt, $e['path']), ENT_XML1).'"/>'."\n";
                }
                $xml .= '    <xhtml:link rel="alternate" hreflang="x-default" href="'.htmlspecialchars($localized($default, $e['path']), ENT_XML1).'"/>'."\n";
                $xml .= "  </url>\n";
            }
        }
        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }


    public function robots(): Response
    {
        $custom = trim((string) site_setting('robots_txt_content', ''));
        if ($custom === '') {
            $custom = "User-agent: *\nAllow: /\n\nDisallow: /admin\nDisallow: /account\nDisallow: /cart\nDisallow: /checkout";
        }

        $custom .= "\n\nSitemap: ".rtrim(config('app.url'), '/')."/sitemap.xml\n";
        return response($custom, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }


    public function pingGoogle()
    {
        $sitemap = url('/sitemap.xml');
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

