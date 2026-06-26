<?php

namespace App\Http\Controllers;

use App\Models\ShippingRate;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(): View
    {
        $seo = [
            'seo_title' => 'Checkout | UNI-LAB MARKET',
            'seo_description' => 'Complete your order securely.',
            'canonical_url' => route('checkout'),
        ];

        $shippingRates = ShippingRate::active()->orderBy('state')->orderBy('city')->get();

        return view('checkout.index', compact('seo', 'shippingRates'));
    }
}
