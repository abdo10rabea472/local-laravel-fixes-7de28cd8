@extends('layouts.front')

@section('content')
  <section class="bg-gradient-to-br from-rose-500 via-pink-600 to-red-700 text-white py-16">
    <div class="max-w-6xl mx-auto px-4 text-center">
      <h1 class="text-4xl font-bold mb-3"><i class="fas fa-fire"></i> {{ __('app.offers_hero_title') }}</h1>
      <p class="text-rose-100">{{ __('app.offers_hero_subtitle') }}</p>
    </div>
  </section>

  @if($coupons->count())
    <section class="py-12 bg-slate-50">
      <div class="max-w-6xl mx-auto px-4">
        <h2 class="text-2xl font-bold text-slate-800 mb-6"><i class="fas fa-ticket text-sky-600"></i>
          {{ __('app.offers_available_coupons') }}</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
          @foreach($coupons as $c)
            <div class="bg-white rounded-2xl border-2 border-dashed border-sky-300 p-5 hover:shadow-lg transition">
              <div class="flex justify-between items-start mb-3">
                <div>
                  <div class="text-xs text-slate-500">{{ __('app.offers_coupon_code') }}</div>
                  <div class="text-2xl font-bold text-sky-700">{{ $c->code }}</div>
                </div>
                <span class="bg-rose-100 text-rose-700 px-3 py-1 rounded-full text-sm font-bold">
                  @if($c->type === 'percent') {{ rtrim(rtrim($c->value, '0'), '.') }}% @else {{ money($c->value) }} @endif
                </span>
              </div>
              @if($c->description)
              <p class="text-sm text-slate-600 mb-2">{{ $c->description }}</p>@endif
              @if($c->ends_at)
                <p class="text-xs text-slate-400"><i class="far fa-clock"></i> {{ __('app.offers_ends_at') }}
              {{ $c->ends_at->format('Y-m-d') }}</p>@endif
              <button
                onclick="navigator.clipboard.writeText('{{ $c->code }}'); this.innerText='{{ __('app.offers_copied') }}'"
                class="mt-3 w-full bg-sky-600 text-white py-2 rounded-lg text-sm hover:bg-sky-700">{{ __('app.offers_copy_code') }}</button>
            </div>
          @endforeach
        </div>
      </div>
    </section>
  @endif

  <section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4">
      <h2 class="text-2xl font-bold text-slate-800 mb-6"><i class="fas fa-tags text-rose-500"></i>
        {{ __('app.offers_discounted_products') }}</h2>

      @if($products->isEmpty())
        <p class="text-center text-slate-500 py-12">{{ __('app.offers_empty') }}</p>
      @else
        <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          @foreach($products as $p)
            @php
              $discount = $p->price > 0 ? round((($p->price - $p->sale_price) / $p->price) * 100) : 0;
            @endphp
            <a href="{{ route('product.show', $p->slug) }}"
              class="block flex-1 flex flex-col focus:outline-none bg-white rounded-3xl border border-slate-200/80 overflow-hidden hover:shadow-xl transition-all duration-300 group relative">

              <!-- حاوية الصورة العلوية مع الشارات الذكية -->
              <div
                class="relative aspect-square w-full bg-slate-50 overflow-hidden flex items-center justify-center p-8 border-b border-slate-100 shadow-[inset_0_-10px_20px_rgba(0,0,0,0.01)]">

                <!-- شارة القسم (Category) -->
                @if($p->category?->name)
                  <span
                    class="absolute top-4 left-4 z-10 px-3 py-1.5 text-[10px] font-black rounded-lg bg-white text-slate-700 shadow-[0_4px_10px_rgba(0,0,0,0.05)] border border-slate-200/60 uppercase tracking-widest"
                    style="color: #db2777">
                    {{ $p->category->name }}
                  </span>
                @endif

                <!-- شارة الخصم (تظهر ديناميكياً بدلاً من النص الثابت إذا وجد خصم) -->
                @if($discount > 0)
                  <div
                    class="absolute top-4 right-4 z-10 bg-gradient-to-br from-rose-500 to-orange-600 text-white font-black text-[10px] px-2.5 py-1.5 rounded-xl shadow-[0_8px_16px_rgba(244,63,94,0.3),inset_0_2px_4px_rgba(255,255,255,0.2)] border-t border-white/20 transform rotate-2 group-hover:scale-110 group-hover:rotate-0 transition-all duration-300">
                    SAVE {{ $discount }}%
                  </div>
                @endif

                <!-- صورة المنتج الديناميكية مع تأثيرات الحركة والـ Fallback في السيرفر والمتصفح -->
                <img
                  src="{{ $p->images->first() ? asset('storage/' . $p->images->first()->image) : (site_setting_url('default_product_image') ?: asset('imges/products/default.jpg')) }}"
                  alt="{{ $p->name }}" loading="lazy"
                  onerror="this.onerror=null;this.src='{{ site_setting_url('default_product_image') ?: asset('imges/products/default.jpg') }}'"
                  class="max-h-full max-w-full object-contain transition-transform duration-500 group-hover:scale-110 group-hover:rotate-1"
                  decoding="async">
              </div>

              <!-- الجزء السفلي: تفاصيل المنتج والسعر -->
              <div class="p-6 flex-1 flex flex-col justify-between bg-gradient-to-b from-white to-slate-50/50">
                <div>
                  <!-- شارة حالة المخزون التحذيرية الذكية (تظهر فقط إذا قارب على النفاد كمثال) -->
                  @if($p->stock > 0 && $p->stock <= 10)
                    <div class="flex items-center justify-between gap-2 mb-2">
                      <span
                        class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-100/70">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        Only {{ $p->stock }} left
                      </span>
                    </div>
                  @endif

                  <!-- اسم المنتج الديناميكي -->
                  <h2
                    class="text-base font-extrabold text-slate-900 tracking-tight transition-colors duration-300 line-clamp-2 group-hover:text-slate-800">
                    {{ $p->name }}
                  </h2>

                  <!-- وصف قصير للمنتج إن وُجد (اختياري، مأخوذ من الاسم أو الحقول المتاحة لديك) -->
                  <p class="text-xs text-slate-500 mt-2 font-medium leading-relaxed line-clamp-2">
                    {{ $p->description ?? 'Premium quality tool optimized for professional and student practice.' }}
                  </p>
                </div>

                <!-- السعر وزر إضافة للسلة -->
                <div class="flex items-center justify-between mt-6 pt-5 border-t border-slate-100">
                  <div class="flex flex-col">
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Investment</span>

                    <!-- السعر الحالي بعد الخصم -->
                    <span class="text-xl font-black font-mono tracking-tight" style="color: #db2777">
                      {{ money($p->sale_price) }}
                    </span>

                  <!-- السعر الأصلي المشطوب (يظهر فقط إذا كان هناك خصم) -->
                    @if($discount > 0)
                      <span class="text-xs text-slate-400 line-through font-mono mt-0.5">
                        {{ money($p->price) }}
                      </span>
                    @endif
                  </div>

                  <!-- زر إضافة إلى السلة -->
                  <button type="button" onclick="event.preventDefault(); event.stopPropagation(); addToCart(this);"
                    class="add-btn h-12 w-12 rounded-2xl bg-gradient-to-b from-white to-slate-100 text-slate-600 flex items-center justify-center border border-slate-200 shadow-[0_4px_6px_-1px_rgba(0,0,0,0.05),inset_0_-2px_4px_rgba(0,0,0,0.05)] transition-all duration-300 group-hover:text-white group-hover:scale-105 active:scale-95"
                    style="--hover-bg: linear-gradient(180deg, #b91c1c, #db2777 );"
                    onmouseover="this.style.background=this.style.getPropertyValue('--hover-bg');this.style.borderColor='#10b981';"
                    onmouseout="this.style.background='';this.style.borderColor='';">
                    <i class="fa-solid fa-cart-shopping text-sm transform group-hover:scale-110 transition-transform"></i>
                  </button>
                </div>
              </div>
            </a>
          @endforeach
        </div>
        <div class="mt-8">{{ $products->links() }}</div>
      @endif
    </div>
  </section>
@endsection