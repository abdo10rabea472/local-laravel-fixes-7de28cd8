@extends('layouts.front')

@section('content')

{{-- ═══════════════ HERO ═══════════════ --}}
<section class="relative min-h-[85vh] sm:min-h-[90vh] flex items-center overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-950"></div>
    @if($hero['background'])
    <div class="absolute inset-0 bg-cover bg-center opacity-15 mix-blend-overlay" style="background-image: url('{{ $hero['background'] }}')"></div>
    @endif
    <div class="absolute top-1/4 -left-32 w-96 h-96 bg-violet-600/20 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 right-0 w-80 h-80 bg-indigo-500/20 rounded-full blur-3xl"></div>

    <div class="relative max-w-[1600px] mx-auto mx-auto px-4 sm:px-6 lg:px-8 py-20 w-full">
        <div class="max-w-3xl">
            @if(!empty($hero['badge']))
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur border border-white/20 rounded-full px-5 py-2 text-sm text-white mb-6">
                <span class="text-amber-400">✦</span>
                <span class="font-medium">{{ $hero['badge'] }}</span>
            </div>
            @endif

            <h1 class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-black text-white leading-[1.08] tracking-tight mb-6">
                {{ $hero['title'] }}
            </h1>
            <p class="text-lg sm:text-xl text-slate-300 max-w-2xl mb-10 leading-relaxed">
                {{ $hero['subtitle'] }}
            </p>

            <div class="flex flex-wrap gap-3 sm:gap-4 mb-12 sm:mb-16">
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center gap-2 bg-amber-400 hover:bg-amber-300 text-slate-950 font-bold text-base px-7 py-3.5 rounded-2xl transition-all hover:scale-[1.02] shadow-lg shadow-amber-500/25">
                    <i class="fa-solid fa-box"></i> Browse Products
                </a>
                <a href="#colleges"
                   class="inline-flex items-center gap-2 border-2 border-white/60 hover:border-white text-white font-bold text-base px-7 py-3.5 rounded-2xl transition-all hover:bg-white/10">
                    Explore Colleges
                    <i class="fa-solid fa-arrow-right text-sm"></i>
                </a>
            </div>

            <div class="grid grid-cols-3 gap-4 sm:gap-8 max-w-lg">
                <div>
                    <div class="text-3xl sm:text-4xl font-black text-white">{{ $hero['stat_products'] }}</div>
                    <div class="text-slate-400 text-sm mt-1">Products</div>
                </div>
                <div>
                    <div class="text-3xl sm:text-4xl font-black text-white">{{ $hero['stat_colleges'] }}</div>
                    <div class="text-slate-400 text-sm mt-1">Colleges</div>
                </div>
                <div>
                    <div class="text-3xl sm:text-4xl font-black text-white">{{ $hero['stat_departments'] }}</div>
                    <div class="text-slate-400 text-sm mt-1">Departments</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════ TRUST BADGES ═══════════════ --}}
<section class="bg-white border-b border-slate-100">
    <div class="max-w-[1600px] mx-auto mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50">
                <div class="h-10 w-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-900">Fast Shipping</p>
                    <p class="text-[11px] text-slate-500">Delivery across Egypt</p>
                </div>
            </div>
            <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50">
                <div class="h-10 w-10 rounded-full bg-violet-100 text-violet-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-900">Secure Payment</p>
                    <p class="text-[11px] text-slate-500">100% protected checkout</p>
                </div>
            </div>
            <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50">
                <div class="h-10 w-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-rotate-left"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-900">Easy Returns</p>
                    <p class="text-[11px] text-slate-500">30-day return policy</p>
                </div>
            </div>
            <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50">
                <div class="h-10 w-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-900">Expert Support</p>
                    <p class="text-[11px] text-slate-500">Technical assistance</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════ SHOP BY COLLEGE ═══════════════ --}}
<section id="colleges" class="py-16 sm:py-20 bg-gradient-to-b from-slate-50 to-white scroll-mt-20">
    <div class="max-w-[1600px] mx-auto mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-14">
            <span class="inline-block text-xs font-bold uppercase tracking-widest text-violet-600 bg-violet-50 px-4 py-1.5 rounded-full mb-4">Shop by College</span>
            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">Find Tools for Your Field</h2>
            <p class="text-slate-500 mt-3 text-sm sm:text-base">Browse equipment tailored to your college and department</p>
        </div>

        @if($mainCategories->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
            @foreach($mainCategories as $college)
                @include('components.college-card', ['college' => $college])
            @endforeach
        </div>
        @else
        <div class="text-center py-16 bg-white rounded-3xl border border-dashed border-slate-200">
            <i class="fa-solid fa-building-columns text-4xl text-slate-300 mb-4"></i>
            <p class="text-slate-500">No colleges added yet.</p>
        </div>
        @endif
    </div>
</section>

{{-- ═══════════════ FEATURED PRODUCTS ═══════════════ --}}
@if($featuredProducts->isNotEmpty())
<section id="featured" class="py-16 sm:py-20 bg-white scroll-mt-20">
    <div class="max-w-[1600px] mx-auto mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 sm:mb-12">
            <div>
                <span class="inline-block text-xs font-bold uppercase tracking-widest text-amber-600 bg-amber-50 px-4 py-1.5 rounded-full mb-3">Featured</span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900">{{ $homeSections['featured_title'] }}</h2>
                <p class="text-slate-500 mt-2 text-sm sm:text-base">{{ $homeSections['featured_subtitle'] }}</p>
            </div>
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 text-violet-600 font-bold text-sm hover:gap-3 transition-all">
                View all <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4 sm:gap-6">
            @foreach($featuredProducts as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══════════════ PROMO BANNER ═══════════════ --}}
<section class="py-8 sm:py-12">
    <div class="max-w-[1600px] mx-auto mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-xl">
            <div class="absolute -right-10 -top-10 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -left-10 -bottom-10 h-48 w-48 rounded-full bg-amber-400/20 blur-3xl"></div>
            <div class="relative px-6 py-10 sm:px-12 sm:py-12 flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <span class="inline-block text-xs font-bold uppercase tracking-wider bg-white/20 px-3 py-1 rounded-full mb-3">Limited Time</span>
                    <h3 class="text-2xl sm:text-3xl font-black mb-2">Special Discount for Universities</h3>
                    <p class="text-violet-100 max-w-xl">Bulk orders for colleges and laboratories receive extra discounts and dedicated support.</p>
                </div>
                <a href="{{ route('products.index') }}" class="shrink-0 inline-flex items-center gap-2 bg-white text-violet-600 hover:bg-slate-50 font-bold px-7 py-3.5 rounded-2xl transition-colors shadow-lg">
                    Explore Offers <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════ ALL PRODUCTS ═══════════════ --}}
<section id="products" class="py-16 sm:py-20 bg-slate-50 scroll-mt-20">
    <div class="max-w-[1600px] mx-auto mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 sm:mb-12">
            <div>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900">{{ $homeSections['products_title'] }}</h2>
                <p class="text-slate-500 mt-2 text-sm sm:text-base">
                    @if(request('search'))
                        Results for "{{ request('search') }}" — {{ $products->total() }} found
                    @else
                        {{ $homeSections['products_subtitle'] ?: 'Browse all available products' }}
                        — {{ $products->total() }} products available
                    @endif
                </p>
            </div>
        </div>

        @if($products->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4 sm:gap-6">
            @foreach($products as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
        <div class="mt-10 flex justify-center">{{ $products->withQueryString()->links() }}</div>
        @else
        <div class="text-center py-16 bg-white rounded-3xl border border-slate-200">
            <i class="fa-solid fa-magnifying-glass text-4xl text-slate-300 mb-4"></i>
            <p class="text-slate-500">No products found.</p>
        </div>
        @endif
    </div>
</section>

{{-- ═══════════════ NEWSLETTER ═══════════════ --}}
<section class="py-16 sm:py-20 bg-white border-t border-slate-100">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center justify-center h-14 w-14 rounded-2xl bg-violet-100 text-violet-600 text-2xl mb-5">
            <i class="fa-regular fa-envelope"></i>
        </div>
        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mb-3">Stay Updated</h2>
        <p class="text-slate-500 mb-8">Subscribe to get the latest products, offers, and educational equipment news.</p>
        <form action="#" method="post" class="flex flex-col sm:flex-row gap-3 max-w-lg mx-auto">
            @csrf
            <input type="email" required placeholder="Enter your email" class="flex-1 h-12 px-5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:border-violet-300 focus:bg-white transition-colors">
            <button type="submit" class="h-12 px-8 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl transition-colors">
                Subscribe
            </button>
        </form>
    </div>
</section>

@endsection
