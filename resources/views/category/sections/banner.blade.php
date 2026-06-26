@php
    $bgUrl = $section->background_image
        ? asset('storage/' . $section->background_image)
        : $college?->banner_url;
@endphp
<section class="relative rounded-3xl overflow-hidden min-h-[280px] flex items-center {{ $bgUrl ? '' : 'bg-gradient-to-br from-violet-600 to-indigo-600' }}"
    @if($bgUrl) style="background-image: url('{{ $bgUrl }}'); background-size: cover; background-position: center;" @endif>
    @if($bgUrl)
        <div class="absolute inset-0 bg-slate-900/60"></div>
    @endif
    <div class="relative z-10 p-8 sm:p-12 w-full">
        @if($section->title)
            <h3 class="text-2xl sm:text-3xl font-black text-white mb-3">{{ $section->title }}</h3>
        @endif
        @if($section->content['body'] ?? false)
            <div class="text-white/90 max-w-2xl leading-relaxed">{!! nl2br(e($section->content['body'])) !!}</div>
        @endif
    </div>
</section>
