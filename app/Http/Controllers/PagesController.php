<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class PagesController extends Controller
{
    public function about()
    {
        $stats = Cache::remember('pages.about.stats', 600, function () {
            return [
                'products'   => Product::where('status', true)->count(),
                'categories' => Category::count(),
                'years'      => max(1, now()->year - 2020),
                'customers'  => (int) (\App\Models\User::count() * 1.2 + 50),
            ];
        });

        $team = [
            ['name' => 'د. أحمد العلي', 'role' => 'مدير المعمل', 'image' => null],
            ['name' => 'م. سارة الزهراني', 'role' => 'كبير الكيميائيين', 'image' => null],
            ['name' => 'م. خالد الشمري', 'role' => 'مدير الجودة', 'image' => null],
            ['name' => 'د. منى الحربي', 'role' => 'مسؤولة التطوير', 'image' => null],
        ];

        return view('pages.about', compact('stats', 'team'));
    }
}
