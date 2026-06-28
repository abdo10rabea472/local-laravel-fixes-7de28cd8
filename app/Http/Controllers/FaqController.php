<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = collect();
        if (Schema::hasTable('faqs')) {
            $faqs = Cache::remember('faqs.public', 300, function () {
                return Faq::active()->orderBy('sort_order')->orderBy('id')->get()->groupBy('category');
            });
        }

        return view('pages.faqs', compact('faqs'));
    }
}
