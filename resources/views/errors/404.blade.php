@extends('layouts.front')

@php
    // Smart suggestions for the 404 page: popular products + recent posts + top categories
    try {
        $suggestedProducts = \Illuminate\Support\Facades\Cache::remember('404.suggested.products', 600, function () {
            if (! \Illuminate\Support\Facades\Schema::hasTable('products')) return collect();
            return \App\Models\Product::query()
                ->select(['id', 'name', 'slug', 'price', 'sale_price'])
                ->with(['images' => fn($q) => $q->select(['id','product_id','thumb','medium'])->orderBy('sort_order')->limit(1)])
                ->where('is_active', true)
                ->orderByDesc('id')
                ->limit(6)
                ->get();
        });
    } catch (\Throwable $e) { $suggestedProducts = collect(); }

    try {
        $suggestedCategories = \Illuminate\Support\Facades\Cache::remember('404.suggested.categories', 600, function () {
            if (! \Illuminate\Support\Facades\Schema::hasTable('categories')) return collect();
            return \App\Models\Category::query()
                ->select(['id','name','slug'])
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->limit(6)
                ->get();
        });
    } catch (\Throwable $e) { $suggestedCategories = collect(); }
@endphp

@section('content')
<section class="min-h-[70vh] bg-gradient-to-br from-slate-50 via-violet-50 to-indigo-50 py-20">
    <div class="max-w-5xl mx-auto px-4 text-center">
        <div class="relative inline-block">
            <h1 class="text-[10rem] md:text-[14rem] font-black bg-gradient-to-br from-violet-600 to-indigo-700 bg-clip-text text-transparent leading-none">404</h1>
            <div class="absolute inset-0 flex items-center justify-center -z-0 opacity-10">
                <i class="fas fa-flask text-[12rem] text-violet-600"></i>
            </div>
        </div>

        <h2 class="text-2xl md:text-3xl font-bold text-slate-800 mt-4 mb-3">عذرًا، الصفحة غير موجودة</h2>
        <p class="text-slate-600 mb-6 max-w-md mx-auto leading-relaxed">
            الرابط الذي تحاول الوصول إليه قد يكون منقولًا أو محذوفًا. جرّب البحث أو تصفّح اقتراحاتنا أدناه.
        </p>

        <form action="{{ route('products.index') }}" method="GET" class="max-w-xl mx-auto mb-8 flex gap-2">
            <input type="text" name="search" placeholder="ابحث عن منتج…" class="flex-1 px-4 py-3 rounded-xl border border-slate-200 focus:border-violet-500 focus:ring-2 focus:ring-violet-200 outline-none">
            <button class="px-5 py-3 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-xl"><i class="fas fa-search"></i></button>
        </form>

        <div class="flex flex-wrap gap-3 justify-center mb-12">
            <a href="{{ url('/') }}" class="px-6 py-3 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-xl shadow-lg shadow-violet-500/30 transition"><i class="fas fa-home"></i> الرئيسية</a>
            <a href="{{ route('products.index') }}" class="px-6 py-3 bg-white hover:bg-slate-100 text-slate-700 font-bold rounded-xl border border-slate-200 transition"><i class="fas fa-store"></i> المنتجات</a>
            <a href="{{ route('blog.index') }}" class="px-6 py-3 bg-white hover:bg-slate-100 text-slate-700 font-bold rounded-xl border border-slate-200 transition"><i class="fas fa-newspaper"></i> المدونة</a>
            <a href="{{ route('contact') }}" class="px-6 py-3 bg-white hover:bg-slate-100 text-slate-700 font-bold rounded-xl border border-slate-200 transition"><i class="fas fa-envelope"></i> تواصل معنا</a>
        </div>

        @if($suggestedCategories->isNotEmpty())
            <div class="mb-10">
                <h3 class="text-lg font-bold text-slate-700 mb-4">تصفح الأقسام</h3>
                <div class="flex flex-wrap justify-center gap-2">
                    @foreach($suggestedCategories as $c)
                        <a href="{{ url('/category/'.$c->slug) }}" class="px-4 py-2 bg-white text-slate-700 hover:bg-violet-50 hover:text-violet-700 rounded-full border border-slate-200 text-sm font-medium">{{ $c->name }}</a>
                    @endforeach
                </div>
            </div>
        @endif

        @if($suggestedProducts->isNotEmpty())
            <div>
                <h3 class="text-lg font-bold text-slate-700 mb-4">ربما يعجبك أيضًا</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-start">
                    @foreach($suggestedProducts as $p)
                        @php $img = $p->images->first(); @endphp
                        <a href="{{ url('/product/'.$p->slug) }}" class="block bg-white rounded-xl border border-slate-200 hover:border-violet-400 hover:shadow-lg transition p-3">
                            @if($img)
                                <img src="{{ $img->getUrl('thumb') }}" alt="{{ $p->name }}" class="w-full h-32 object-contain mb-2" loading="lazy" decoding="async">
                            @endif
                            <div class="text-sm font-semibold text-slate-800 line-clamp-2">{{ $p->name }}</div>
                            <div class="text-violet-600 font-bold mt-1">{{ money($p->sale_price ?: $p->price) }}</div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endsection

