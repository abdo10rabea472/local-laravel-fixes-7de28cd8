<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Aggregate all five product stats in a single query instead of 4 round-trips.
        // Result is cached briefly to keep the dashboard snappy under repeated reloads.
        $productStats = Cache::remember('admin.dashboard.product_stats', 60, function () {
            return Product::query()->selectRaw(
                'COUNT(*) as total_products,'
                . ' COALESCE(SUM(stock), 0) as total_stock,'
                . ' COALESCE(SUM(COALESCE(sale_price, price) * stock), 0) as total_stock_value,'
                . ' SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as out_of_stock_count'
            )->first();
        });

        $totalProducts = (int) ($productStats->total_products ?? 0);
        $totalStock = (int) ($productStats->total_stock ?? 0);
        $totalStockValue = (float) ($productStats->total_stock_value ?? 0);
        $outOfStockCount = (int) ($productStats->out_of_stock_count ?? 0);

        $totalCategories = Cache::remember('admin.dashboard.total_categories', 300, fn () => Category::count());

        $categoryStats = Cache::remember('admin.dashboard.category_stats', 300, function () {
            return Product::query()
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->select('categories.name as category_name', DB::raw('count(*) as count'))
                ->groupBy('categories.id', 'categories.name')
                ->orderByDesc('count')
                ->limit(8)
                ->get();
        });

        $recentProducts = Product::query()
            ->select(['id', 'name', 'slug', 'price', 'stock', 'category_id', 'created_at'])
            ->with(['category:id,name'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalStock',
            'totalStockValue',
            'outOfStockCount',
            'totalCategories',
            'categoryStats',
            'recentProducts'
        ));
    }
}
