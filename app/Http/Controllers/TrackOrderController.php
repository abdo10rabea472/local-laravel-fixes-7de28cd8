<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class TrackOrderController extends Controller
{
    public function show(Request $request)
    {
        $order = null;
        $notFound = false;

        if ($request->filled(['order_number', 'email'])) {
            $request->validate([
                'order_number' => 'required|string|max:60',
                'email' => 'required|email|max:150',
            ]);

            $order = Order::with(['items.product:id,slug,name', 'history', 'carrier'])
                ->where('order_number', trim($request->order_number))
                ->where('email', trim($request->email))
                ->first();

            $notFound = !$order;
        }

        return view('pages.track-order', compact('order', 'notFound'));
    }
}
