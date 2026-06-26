{{-- College card for homepage grid --}}
@php
    $primary = $college->primary_color ?? '#6366f1';
    $secondary = $college->secondary_color ?? '#8b5cf6';
@endphp
<a href="{{ route('category.show', $college->slug) }}"
   class="group relative flex flex-col rounded-3xl overflow-hidden border border-slate-200/80 bg-white shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
    <div class="h-28 relative overflow-hidden"
         style="background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }});">
        @if($college->banner_url)
            <img src="{{ $college->banner_url }}" alt="" class="absolute inset-0 w-full h-full object-cover opacity-40 mix-blend-overlay">
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
        <div class="absolute bottom-3 right-3 h-14 w-14 rounded-2xl bg-white/95 shadow-lg flex items-center justify-center p-2">
            @if($college->icon_url)
                <img src="{{ $college->icon_url }}" alt="{{ $college->name }}" class="max-h-full max-w-full object-contain">
            @else
                <span class="text-lg font-black" style="color: {{ $primary }}">{{ strtoupper(substr($college->name, 0, 2)) }}</span>
            @endif
        </div>
    </div>
    <div class="p-5 flex-1 flex flex-col">
        <h3 class="text-lg font-bold text-slate-900 group-hover:text-violet-700 transition-colors">{{ $college->name }}</h3>
        @if($college->description)
            <p class="text-sm text-slate-500 mt-1 line-clamp-2">{{ $college->description }}</p>
        @endif
        <div class="mt-4 flex items-center justify-between text-xs font-semibold text-slate-500">
            <span>{{ $college->children_count ?? $college->children->count() }} departments</span>
            <span class="inline-flex items-center gap-1 text-violet-600 group-hover:gap-2 transition-all">
                Explore
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </span>
        </div>
        @if($college->children->isNotEmpty())
        <div class="mt-3 flex flex-wrap gap-1.5">
            @foreach($college->children->take(3) as $child)
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">{{ $child->name }}</span>
            @endforeach
            @if($college->children->count() > 3)
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-violet-50 text-violet-600">+{{ $college->children->count() - 3 }}</span>
            @endif
        </div>
        @endif
    </div>
</a>
