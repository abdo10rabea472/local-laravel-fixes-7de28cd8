<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class InternalLinker
{
    /** Cached map of [lowercase product name => slug] for active products. */
    public function productMap(): array
    {
        return Cache::remember('internal_linker.products', 600, function () {
            if (! Schema::hasTable('products')) return [];
            $q = Product::query()->select(['name','slug']);
            if (Schema::hasColumn('products','is_active')) $q->where('is_active', true);
            return $q->limit(2000)->get()
                ->filter(fn($p) => $p->slug && mb_strlen($p->name) >= 4)
                ->mapWithKeys(fn($p) => [mb_strtolower($p->name) => $p->slug])
                ->all();
        });
    }

    /**
     * Wrap product-name mentions in $html with links to /product/{slug}.
     * Each product is linked at most once. Skips text already inside <a>, <code>, <pre>.
     */
    public function linkProductMentions(?string $html, int $maxLinks = 5): string
    {
        $html = (string) $html;
        if ($html === '') return '';

        $map = $this->productMap();
        if (empty($map)) return $html;

        // Sort by length desc so longer names match before shorter substrings
        uksort($map, fn($a, $b) => mb_strlen($b) - mb_strlen($a));

        $linked = 0;
        foreach ($map as $nameLower => $slug) {
            if ($linked >= $maxLinks) break;
            $name = preg_quote($nameLower, '/');
            // Match the name as a standalone phrase, NOT inside an existing <a>...</a> or attribute
            $pattern = '/(?<![\w>])(' . $name . ')(?![\w<])(?![^<]*<\/a>)/iu';
            $count = 0;
            $html = preg_replace_callback($pattern, function ($m) use ($slug, &$count) {
                if ($count > 0) return $m[0]; // first occurrence only
                $count++;
                return '<a href="'.url('/product/'.$slug).'" class="text-violet-600 hover:underline font-medium">'.$m[1].'</a>';
            }, $html, 1);
            if ($count > 0) $linked++;
        }
        return $html;
    }
}
