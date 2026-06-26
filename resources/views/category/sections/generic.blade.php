@php
    $bgUrl = $section->background_image
        ? asset('storage/' . $section->background_image)
        : $college?->banner_url;
@endphp
@if($section->title || $section->content || $section->image)
<section class="relative rounded-3xl overflow-hidden {{ $bgUrl ? '' : 'bg-white border border-slate-200' }}"
    @if($bgUrl) style="background-image: url('{{ $bgUrl }}'); background-size: cover; background-position: center;" @endif>
    @if($bgUrl)
        <div class="absolute inset-0 bg-slate-900/50"></div>
    @endif
    <div class="relative z-10 p-6 sm:p-8">
        @if($section->title)
            <h3 class="text-xl font-bold mb-3 {{ $bgUrl ? 'text-white' : 'text-slate-900' }}">{{ $section->title }}</h3>
        @endif
        @if($section->image)
            <img src="{{ asset('storage/' . $section->image) }}" alt="" class="rounded-2xl mb-4 max-h-64 object-cover">
        @endif
        @if(is_array($section->content))
            <pre class="text-sm {{ $bgUrl ? 'text-white/90' : 'text-slate-600' }} whitespace-pre-wrap">{{ json_encode($section->content, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
        @elseif($section->content)
            <p class="{{ $bgUrl ? 'text-white/90' : 'text-slate-700' }}">{{ $section->content }}</p>
        @endif
    </div>
</section>
@endif
