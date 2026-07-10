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
            $coupons = Coupon::active()
                ->orderByDesc('created_at')
                ->limit(8)
                ->get();
        }

        return view('pages.offers', compact('products', 'coupons'));
    }
}
