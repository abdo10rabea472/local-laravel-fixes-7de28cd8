<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Product;

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

        $coupons = Coupon::query()
            ->where('is_active', true)
            ->orderByDesc('id')
            ->limit(24)
            ->get();

        return view('pages.offers', compact('products', 'coupons'));
    }
}
