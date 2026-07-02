@php
  $primaryImage = $product->images->first();
  $imageUrl = $primaryImage
    ? $primaryImage->getUrl('medium')
    : site_setting_url('default_product_image', asset('imges/products/default.jpg'));
  $displayPrice = $product->effective_price;
  $compareAt = $product->compare_at_price;
  $hasDiscount = $compareAt !== null && $compareAt > $displayPrice;
  $discountPercent = $product->discount_percent;
  $inStock = $product->stock > 0;
@endphp
@php $convertedPrice = convert_price($displayPrice); @endphp
<article
  class="group flex flex-col rounded-2xl bg-white border border-slate-200/80 overflow-hidden shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300"
  data-id="{{ $product->id }}" data-price="{{ $convertedPrice }}">

  <a href="{{ route('product.show', $product->slug) }}"
    class="relative aspect-square bg-slate-50 flex items-center justify-center p-4 overflow-hidden">
    @if($hasDiscount && $discountPercent > 0)
      <span
        class="absolute top-4 left-4 z-10 bg-gradient-to-br from-rose-500 to-red-600 text-white font-black text-[11px] px-3 py-1.5 rounded-xl shadow-[0_8px_16px_rgba(244,63,94,0.3),inset_0_2px_4px_rgba(255,255,255,0.2)] border-t border-white/20 transform rotate-6 group-hover:scale-110 group-hover:rotate-0 transition-all duration-300">SAVE 
         {{ $discountPercent }}%</span>
    @endif
    @if($product->featured ?? false)
      <span
        class="absolute top-4 right-4 z-10 bg-gradient-to-br from-amber-500 to-orange-600 text-white font-black text-[10px] px-2.5 py-1.5 rounded-xl shadow-[0_8px_16px_rgba(245,158,11,0.3),inset_0_2px_4px_rgba(255,255,255,0.2)] border-t border-white/20 transform rotate-2 group-hover:scale-110 group-hover:rotate-0 transition-all duration-300">{{ __('app.shared_featured') }}</span>
    @endif
    <img src="{{ $imageUrl }}" alt="{{ $product->name }}" loading="lazy"
      onerror="this.onerror=null;this.src='{{ site_setting_url('default_product_image') ?: asset('imges/products/default.jpg') }}'"
      class="max-h-full max-w-full object-contain transition-transform duration-500 group-hover:scale-105">
  </a>
  <div class="p-4 flex flex-col flex-1">
    @if($product->relationLoaded('category') && $product->category)
      <span
        class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-blue-600 bg-gradient-to-r from-blue-50 to-sky-50 rounded-full border border-blue-100 shadow-sm mb-3">{{ $product->category->name }}</span>
    @endif
    <h3 class="font-bold text-slate-900 line-clamp-1 group-hover:text-blue-700 transition-colors">
      <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
    </h3>
    @if($product->short_description)
      <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $product->short_description }}</p>
    @endif
    <div class="mt-auto pt-4 flex items-end justify-between gap-2 border-t border-slate-100 mt-3">
      <div>
        @if($hasDiscount)
          <del class="text-xs text-slate-400 block">{{ money($compareAt) }}</del>
        @endif
        <span class="text-lg font-black text-slate-900">{{ money($displayPrice) }}</span>
      </div>

      @if($inStock)
        <button type="button" onclick="event.preventDefault(); addToCart(this);"
          class="h-12 w-12 rounded-2xl bg-gradient-to-b from-white to-blue-100 text-blue-600 flex items-center justify-center border border-slate-200 shadow-[0_4px_6px_-1px_rgba(0,0,0,0.05),inset_0_-2px_4px_rgba(0,0,0,0.05)] transition-all duration-300 group-hover:from-blue-500 group-hover:to-blue-600 group-hover:text-white group-hover:border-blue-600 group-hover:shadow-[0_10px_20px_rgba(147,51,234,0.3)] group-hover:scale-105 active:scale-95 focus:outline-none">
          <!-- {{ __('app.product_add') }} -->
          <i class="fa-solid fa-cart-shopping text-sm transform group-hover:scale-110 transition-transform"></i>
        </button>
      @else
        <span
          class="inline-flex items-center px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-rose-600 bg-rose-50 border border-rose-100 rounded-2xl shadow-sm">
          {{ __('app.shared_out_of_stock') }}
        </span>
      @endif
    </div>
  </div>
</article>