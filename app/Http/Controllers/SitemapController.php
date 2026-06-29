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
    /** Max URLs per sitemap file (spec: 50,000). */
    private const PER_FILE = 45000;

    /** ===================== INDEX (sitemap.xml) ===================== */
    public function index(): Response
    {
        $base = rtrim(config('app.url'), '/');
        $now  = now()->toAtomString();
        $maps = [];

        $maps[] = ['loc' => $base.'/sitemap-static.xml', 'lastmod' => $now];

        if (\Schema::hasTable('pages')) {
            $maps[] = ['loc' => $base.'/sitemap-pages.xml', 'lastmod' => $now];
        }
        if (\Schema::hasTable('categories')) {
            $maps[] = ['loc' => $base.'/sitemap-categories.xml', 'lastmod' => $now];
        }

        if (\Schema::hasTable('products')) {
            $total = \App\Models\Product::query()
                ->when(\Schema::hasColumn('products','status'), fn($q) => $q->where('status', 1))
                ->count();
            $pages = max(1, (int) ceil($total / self::PER_FILE));
            for ($i = 1; $i <= $pages; $i++) {
                $maps[] = ['loc' => $base."/sitemap-products-{$i}.xml", 'lastmod' => $now];
            }
        }

        if (\Schema::hasTable('blog_posts')) {
            $total = \App\Models\BlogPost::published()->count();
            $pages = max(1, (int) ceil($total / self::PER_FILE));
            for ($i = 1; $i <= $pages; $i++) {
                $maps[] = ['loc' => $base."/sitemap-blog-{$i}.xml", 'lastmod' => $now];
            }
        }

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($maps as $m) {
            $xml .= "  <sitemap>\n";
            $xml .= "    <loc>".htmlspecialchars($m['loc'], ENT_XML1)."</loc>\n";
            $xml .= "    <lastmod>{$m['lastmod']}</lastmod>\n";
            $xml .= "  </sitemap>\n";
        }
        $xml .= '</sitemapindex>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /** ===================== STATIC ROUTES ===================== */
    public function staticPages(): Response
    {
        $now = now()->toAtomString();
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

        $entries = [];
        foreach ($statics as [$name, $freq, $pri]) {
            try {
                $entries[] = ['loc' => route($name), 'lastmod' => $now, 'changefreq' => $freq, 'priority' => $pri];
            } catch (\Throwable $e) {}
        }
        return $this->renderUrlset($entries);
    }

    /** ===================== CMS PAGES ===================== */
    public function pages(): Response
    {
        $reserved = ['about','faqs','privacy-policy','returns-refunds','payment-success','checkout','contact','blog','offers'];
        $entries = [];
        if (\Schema::hasTable('pages')) {
            Page::query()
                ->when(\Schema::hasColumn('pages','is_active'), fn($q) => $q->where('is_active', 1))
                ->whereNotIn('slug', $reserved)
                ->select(['slug','updated_at'])
                ->limit(self::PER_FILE)
                ->get()->each(function ($p) use (&$entries) {
                    if (!$p->slug) return;
                    $entries[] = [
                        'loc' => route('pages.show', $p->slug),
                        'lastmod' => optional($p->updated_at)?->toAtomString(),
                        'changefreq' => 'monthly', 'priority' => '0.5',
                    ];
                });
        }
        return $this->renderUrlset($entries);
    }

    /** ===================== CATEGORIES ===================== */
    public function categories(): Response
    {
        $entries = [];
        if (\Schema::hasTable('categories')) {
            Category::query()
                ->select(['slug','updated_at'])
                ->orderByDesc('updated_at')
                ->limit(self::PER_FILE)
                ->get()->each(function ($c) use (&$entries) {
                    if (!$c->slug) return;
                    $entries[] = [
                        'loc' => route('category.show', $c->slug),
                        'lastmod' => optional($c->updated_at)?->toAtomString(),
                        'changefreq' => 'weekly', 'priority' => '0.7',
                    ];
                });
        }
        return $this->renderUrlset($entries);
    }

    /** ===================== PRODUCTS (paginated) ===================== */
    public function products(int $page): Response
    {
        $entries = [];
        if (\Schema::hasTable('products')) {
            Product::query()
                ->when(\Schema::hasColumn('products','status'), fn($q) => $q->where('status', 1))
                ->select(['slug','updated_at'])
                ->orderBy('id')
                ->forPage($page, self::PER_FILE)
                ->get()->each(function ($p) use (&$entries) {
                    if (!$p->slug) return;
                    $entries[] = [
                        'loc' => route('product.show', $p->slug),
                        'lastmod' => optional($p->updated_at)?->toAtomString(),
                        'changefreq' => 'weekly', 'priority' => '0.8',
                    ];
                });
        }
        if (empty($entries)) abort(404);
        return $this->renderUrlset($entries);
    }

    /** ===================== BLOG (paginated) ===================== */
    public function blog(int $page): Response
    {
        $entries = [];
        if (\Schema::hasTable('blog_posts')) {
            BlogPost::published()
                ->select(['slug','updated_at'])
                ->orderBy('id')
                ->forPage($page, self::PER_FILE)
                ->get()->each(function ($b) use (&$entries) {
                    if (!$b->slug) return;
                    $entries[] = [
                        'loc' => route('blog.show', $b->slug),
                        'lastmod' => optional($b->updated_at)?->toAtomString(),
                        'changefreq' => 'weekly', 'priority' => '0.6',
                    ];
                });
        }
        if (empty($entries)) abort(404);
        return $this->renderUrlset($entries);
    }

    /** Render a <urlset> XML response from an entries array. */
    private function renderUrlset(array $entries): Response
    {
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($entries as $e) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>".htmlspecialchars($e['loc'], ENT_XML1)."</loc>\n";
            if (!empty($e['lastmod'])) $xml .= "    <lastmod>{$e['lastmod']}</lastmod>\n";
            if (!empty($e['changefreq'])) $xml .= "    <changefreq>{$e['changefreq']}</changefreq>\n";
            if (!empty($e['priority'])) $xml .= "    <priority>{$e['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';
        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }



    public function robots(): Response
    {
        $custom = trim((string) site_setting('robots_txt_content', ''));
        if ($custom === '') {
            $custom = "User-agent: *\nAllow: /\n\n"
                ."Disallow: /admin\nDisallow: /account\nDisallow: /cart\nDisallow: /checkout";
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

