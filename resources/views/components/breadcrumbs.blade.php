@props(['items' => [], 'emitSchema' => false])

@php
    // Normalize items into [['name' => ..., 'url' => ... (optional)]]
    $items = collect($items)->filter()->values();
    $crumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $items->map(fn ($it, $i) => array_filter([
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => $it['name'] ?? '',
            'item' => $it['url'] ?? null,
        ]))->values()->all(),
    ];
@endphp

@if($items->isNotEmpty())
<nav aria-label="Breadcrumb" class="text-sm text-slate-500 py-3">
    <ol class="flex flex-wrap items-center gap-2">
        @foreach($items as $i => $it)
            <li class="flex items-center gap-2">
                @if(!empty($it['url']) && !$loop->last)
                    <a href="{{ $it['url'] }}" class="hover:text-indigo-600 transition-colors">{{ $it['name'] }}</a>
                @else
                    <span aria-current="page" class="text-slate-700 font-medium">{{ $it['name'] }}</span>
                @endif
                @unless($loop->last)
                    <span class="text-slate-300" aria-hidden="true">/</span>
                @endunless
            </li>
        @endforeach
    </ol>
</nav>
<script type="application/ld+json">{!! json_encode($crumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@if($emitSchema)
<script type="application/ld+json">{!! json_encode($crumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif
@endif

