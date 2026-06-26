@extends('layouts.front')

@section('content')
@php
    $primary = $themeCategory?->primary_color ?? '#6366f1';
    $secondary = $themeCategory?->secondary_color ?? '#8b5cf6';
    $college = $isCollege ? $category : $category->parent;
    $totalProducts = $products->total();
@endphp

{{-- Hero --}}
<section class="relative overflow-hidden">
    <div class="absolute inset-0" style="background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }});"></div>
    @if($category->banner_url ?? $college?->banner_url)
        <img src="{{ $category->banner_url ?? $college?->banner_url }}" alt="" class="absolute inset-0 w-full h-full object-cover opacity-25 mix-blend-overlay">
    @endif
    <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-black/10 to-slate-900/60"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
        <nav class="text-sm text-white/70 mb-6 flex flex-wrap items-center gap-x-2 gap-y-1">
            <a href="{{ route('home') }}" class="hover:text-white">Home</a>
            <span>/</span>
            @if(!$isCollege && $college)
                <a href="{{ route('category.show', $college->slug) }}" class="hover:text-white">{{ $college->name }}</a>
                <span>/</span>
            @endif
            <span class="text-white font-semibold">{{ $category->name }}</span>
        </nav>

        <div class="grid lg:grid-cols-5 gap-8 items-end">
            <div class="lg:col-span-3 flex items-start gap-4">
                <div class="h-16 w-16 sm:h-20 sm:w-20 rounded-2xl bg-white shadow-xl flex items-center justify-center p-2.5 shrink-0">
                    @if($category->icon_url ?? $college?->icon_url)
                        <img src="{{ $category->icon_url ?? $college?->icon_url }}" alt="" class="max-h-full max-w-full object-contain">
                    @else
                        <span class="text-xl font-black" style="color: {{ $primary }}">{{ strtoupper(substr($category->name, 0, 2)) }}</span>
                    @endif
                </div>
                <div>
                    @if(!$isCollege && $college)
                        <span class="text-[11px] font-bold uppercase tracking-wider text-white/60">{{ $college->name }}</span>
                    @endif
                    <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight">{{ $category->name }}</h1>
                    @if($category->description)
                        <p class="text-white/80 mt-2 text-sm sm:text-base leading-relaxed max-w-xl">{{ $category->description }}</p>
                    @endif
                    <div class="flex flex-wrap gap-2 mt-4">
                        <span class="px-3 py-1 rounded-full bg-white/15 text-white text-xs font-bold border border-white/20">
                            {{ $totalProducts }} products
                        </span>
                        @if($departments->isNotEmpty())
                        <span class="px-3 py-1 rounded-full bg-white/15 text-white text-xs font-bold border border-white/20">
                            {{ $departments->count() }} departments
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <form action="{{ route('category.show', $category->slug) }}" method="get" class="lg:col-span-2 w-full">
                <div class="bg-white/10 backdrop-blur border border-white/20 rounded-2xl p-2 flex gap-2">
                    <div class="relative flex-1">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-white/50 text-sm"></i>
                        <input type="search" name="search" value="{{ request('search') }}"
                            placeholder="Search products..."
                            class="w-full h-11 pl-10 pr-3 bg-white rounded-xl text-sm text-slate-800 outline-none">
                    </div>
                    <button type="submit" class="h-11 px-4 bg-white font-bold text-sm rounded-xl shrink-0" style="color: {{ $primary }}">
                        <i class="fa-solid fa-magnifying-glass sm:hidden"></i>
                        <span class="hidden sm:inline">Search</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

{{-- Departments --}}
@if($departments->isNotEmpty())
<section class="bg-white border-b border-slate-200 sticky top-[4rem] lg:top-[5.5rem] z-30 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center gap-3 overflow-x-auto pb-1 scrollbar-thin">
            @if($college)
            <a href="{{ route('category.show', $college->slug) }}"
               class="shrink-0 px-4 py-2 rounded-full text-sm font-bold transition-all
                      {{ $isCollege && !request('search') ? 'text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-violet-50' }}"
               @if($isCollege && !request('search')) style="background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }});" @endif>
                All {{ $college->name }}
            </a>
            @endif
            @foreach($departments as $dept)
            <a href="{{ route('category.show', $dept->slug) }}"
               class="shrink-0 px-4 py-2 rounded-full text-sm font-bold transition-all inline-flex items-center gap-1.5
                      {{ !$isCollege && $category->slug === $dept->slug ? 'text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-violet-50 hover:text-violet-700' }}"
               @if(!$isCollege && $category->slug === $dept->slug) style="background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }});" @endif>
                {{ $dept->name }}
                @if($dept->products_count > 0)
                <span class="text-[10px] opacity-75">({{ $dept->products_count }})</span>
                @endif
            </a>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-slate-50 py-8 sm:py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-xl font-black text-slate-900 mb-5">
            {{ $college?->name ?? $category->name }} Departments
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($departments as $dept)
                @include('components.department-card', [
                    'department' => $dept,
                    'parentCollege' => $college,
                    'currentSlug' => $category->slug,
                ])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Dynamic Page Sections --}}
@if($category->sections && $category->sections->isNotEmpty())
<section class="py-10 sm:py-14 bg-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        @foreach($category->sections as $section)
            @include('category.sections.' . (in_array($section->section_type, ['banner', 'text_block', 'html_block']) ? $section->section_type : 'generic'), [
                'section' => $section,
                'college' => $college,
            ])
        @endforeach
    </div>
</section>
@endif

{{-- Products --}}
<section class="py-10 sm:py-14 bg-white" id="products">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900">
                    {{ $isCollege ? 'All ' . $category->name . ' Products' : $category->name . ' Products' }}
                </h2>
                <p class="text-slate-500 text-sm mt-1">
                    @if(request('search'))
                        {{ $totalProducts }} results for "{{ request('search') }}"
                    @else
                        {{ $totalProducts }} product{{ $totalProducts !== 1 ? 's' : '' }} available
                    @endif
                </p>
            </div>
            <form action="{{ route('category.show', $category->slug) }}" method="get" class="flex items-center gap-2">
                @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                <select name="sort" onchange="this.form.submit()"
                    class="h-10 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold outline-none">
                    <option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest</option>
                    <option value="price_asc" @selected(request('sort') === 'price_asc')>Price ↑</option>
                    <option value="price_desc" @selected(request('sort') === 'price_desc')>Price ↓</option>
                    <option value="name" @selected(request('sort') === 'name')>A → Z</option>
                </select>
            </form>
        </div>

        @if($products->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
            @foreach($products as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
        <div class="mt-10 flex justify-center">{{ $products->links() }}</div>
        @else
        <div class="text-center py-20 bg-slate-50 rounded-3xl border border-slate-200">
            <i class="fa-solid fa-box-open text-4xl text-slate-300 mb-4"></i>
            <h3 class="text-lg font-bold text-slate-800">No products found</h3>
            <p class="text-slate-500 text-sm mt-2">
                @if(request('search'))
                    Try another search term.
                @else
                    Products will appear here once added to this {{ $isCollege ? 'college' : 'department' }}.
                @endif
            </p>
            @if(request('search'))
            <a href="{{ route('category.show', $category->slug) }}" class="inline-block mt-4 text-violet-600 font-bold text-sm">Clear search</a>
            @endif
        </div>
        @endif
    </div>
</section>
@endsection
