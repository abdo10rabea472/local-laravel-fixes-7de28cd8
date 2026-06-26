<?php

namespace App\Services;

use App\Models\Category;
use App\Models\HeaderMenuItem;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class NavigationService
{
    public static function getData(): array
    {
        return Cache::remember('nav_data', 3600, function () {
            $colleges = Category::query()
                ->roots()
                ->active()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->select(['id', 'name', 'slug', 'image', 'primary_color', 'secondary_color'])
                ->withCount(['children' => fn ($q) => $q->where('status', true)])
                ->with([
                    'children' => fn ($q) => $q->active()
                        ->orderBy('sort_order')
                        ->orderBy('name')
                        ->select(['id', 'parent_id', 'name', 'slug', 'image'])
                        ->withCount(['products' => fn ($pq) => $pq->where('status', true)]),
                ])
                ->get();

            $headerMenu = HeaderMenuItem::root()
                ->byLocation('header_primary')
                ->active()
                ->with(['children' => fn ($q) => $q->active()->orderBy('position')])
                ->orderBy('position')
                ->get();

            $topMenu = HeaderMenuItem::root()
                ->byLocation('header_top')
                ->active()
                ->with(['children' => fn ($q) => $q->active()->orderBy('position')])
                ->orderBy('position')
                ->get();

            $footerMenu = HeaderMenuItem::root()
                ->byLocation('footer')
                ->active()
                ->with(['children' => fn ($q) => $q->active()->orderBy('position')])
                ->orderBy('position')
                ->get();

            return [
                'colleges' => $colleges,
                'totalProducts' => Product::active()->count(),
                'totalColleges' => $colleges->count(),
                'headerMenu' => $headerMenu,
                'topMenu' => $topMenu,
                'footerMenu' => $footerMenu,
            ];
        });
    }

    public static function clearCache(): void
    {
        Cache::forget('nav_data');
        Cache::forget('main_categories');
        Cache::forget('homepage_stats');
    }
}
