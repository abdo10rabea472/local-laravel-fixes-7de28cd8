<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalStock = Product::sum('stock');
        $totalStockValue = Product::selectRaw('SUM(COALESCE(sale_price, price) * stock) as total_value')->first()->total_value ?? 0;
        $outOfStockCount = Product::where('stock', 0)->count();
        $totalCategories = Category::count();

        $categoryStats = Product::query()
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name as category_name', DB::raw('count(*) as count'))
            ->groupBy('categories.name')
            ->orderByDesc('count')
            ->limit(8)
            ->get();

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
