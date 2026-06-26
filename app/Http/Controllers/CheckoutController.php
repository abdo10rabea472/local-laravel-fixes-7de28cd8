<?php

namespace App\Http\Controllers;

use App\Models\ShippingCountry;
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

        $shippingCountries = ShippingCountry::active()
            ->with(['regions' => fn ($q) => $q->where('status', true)])
            ->orderBy('position')
            ->orderBy('name')
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'cost' => $c->cost !== null ? (float) $c->cost : null,
                'regions' => $c->regions->map(fn ($r) => [
                    'id' => $r->id,
                    'name' => $r->name,
                    'cost' => $r->cost !== null ? (float) $r->cost : null,
                ])->values(),
            ])->values();

        return view('checkout.index', compact('seo', 'shippingCountries'));
    }
}
