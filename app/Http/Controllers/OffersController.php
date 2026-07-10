<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;

class OffersController extends Controller
{
    public function index()
    {
        $products = Product::with(['category:id,name', 'images:id,product_id,image', 'activeDiscount'])
            ->where('status', true)
            ->where(function ($q) {
                $q->whereHas('discounts', fn ($d) => $d->active())
                  ->orWhere(function ($q2) {
                      $q2->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price');
                  });
            })
            ->orderByDesc('id')
            ->paginate(20);


        $coupons = collect();
        if (Schema::hasTable('coupons')) {
            $now = now();
            $coupons = Coupon::where('is_active', true)
                ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
                ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now))
                ->where(function ($q) {
                    $q->whereNull('usage_limit')
                      ->orWhereRaw('COALESCE(used_count,0) < usage_limit');
                })
                ->orderByDesc('id')
                ->limit(24)
                ->get();
        }

        return view('pages.offers', compact('products', 'coupons'));
    }
}
