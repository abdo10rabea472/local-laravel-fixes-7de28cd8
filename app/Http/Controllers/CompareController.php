<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    private const KEY = 'compare_products';
    private const MAX = 4;

    public function index()
    {
        $ids = (array) session(self::KEY, []);
        $products = Product::with(['category:id,name', 'images:id,product_id,path'])
            ->whereIn('id', $ids)
            ->get();

        return view('pages.compare', compact('products'));
    }

    public function add(Request $request)
    {
        $data = $request->validate(['product_id' => 'required|integer|exists:products,id']);
        $ids = (array) session(self::KEY, []);

        if (in_array($data['product_id'], $ids)) {
            return back()->with('info', 'المنتج موجود في المقارنة');
        }

        if (count($ids) >= self::MAX) {
            return back()->with('error', 'الحد الأقصى ' . self::MAX . ' منتجات للمقارنة');
        }

        $ids[] = (int) $data['product_id'];
        session([self::KEY => $ids]);

        return back()->with('success', 'تمت إضافة المنتج للمقارنة');
    }

    public function remove(Request $request)
    {
        $id = (int) $request->input('product_id');
        $ids = array_values(array_filter((array) session(self::KEY, []), fn($v) => (int)$v !== $id));
        session([self::KEY => $ids]);
        return back();
    }

    public function clear()
    {
        session()->forget(self::KEY);
        return back();
    }
}
