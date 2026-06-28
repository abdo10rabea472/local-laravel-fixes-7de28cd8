<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $items = Wishlist::where('user_id', Auth::id())
            ->with(['product:id,slug,name,price,sale_price,stock,category_id', 'product.images:id,product_id,path'])
            ->latest()
            ->paginate(20);

        return view('pages.wishlist', compact('items'));
    }

    public function toggle(Request $request)
    {
        $data = $request->validate(['product_id' => 'required|integer|exists:products,id']);

        $existing = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $data['product_id'])->first();

        if ($existing) {
            $existing->delete();
            $status = 'removed';
        } else {
            Wishlist::create(['user_id' => Auth::id(), 'product_id' => $data['product_id']]);
            $status = 'added';
        }

        $count = Wishlist::where('user_id', Auth::id())->count();

        if ($request->wantsJson()) {
            return response()->json(['status' => $status, 'count' => $count]);
        }

        return back()->with('success', $status === 'added' ? 'تمت الإضافة للمفضلة' : 'تمت الإزالة من المفضلة');
    }

    public function destroy(Wishlist $wishlist)
    {
        abort_unless($wishlist->user_id === Auth::id(), 403);
        $wishlist->delete();
        return back()->with('success', 'تمت الإزالة من المفضلة');
    }
}
