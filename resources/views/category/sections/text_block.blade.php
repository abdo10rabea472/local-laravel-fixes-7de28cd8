@php
    $bgUrl = $section->background_image
        ? asset('storage/' . $section->background_image)
        : $college?->banner_url;
@endphp
@if($section->title || ($section->content['body'] ?? null))
<section class="relative rounded-3xl overflow-hidden {{ $bgUrl ? '' : 'bg-white border border-slate-200' }}"
    @if($bgUrl) style="background-image: url('{{ $bgUrl }}'); background-size: cover; background-position: center;" @endif>
    @if($bgUrl)
        <div class="absolute inset-0 bg-slate-900/50"></div>
    @endif
    <div class="relative z-10 p-6 sm:p-8">
        @if($section->title)<h3 class="text-xl font-bold mb-3 {{ $bgUrl ? 'text-white' : 'text-slate-900' }}">{{ $section->title }}</h3>@endif
        @if($section->content['body'] ?? false)
            <div class="prose max-w-none {{ $bgUrl ? 'text-white/90' : 'text-slate-700' }}">{!! nl2br(e($section->content['body'])) !!}</div>
        @endif
    </div>
</section>
@endif
