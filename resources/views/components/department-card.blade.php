@php
    $primary = $department->primary_color ?? $themePrimary ?? '#6366f1';
    $secondary = $department->secondary_color ?? $themeSecondary ?? '#8b5cf6';
    if (! isset($themePrimary)) {
        $primary = $parentCollege->primary_color ?? '#6366f1';
        $secondary = $parentCollege->secondary_color ?? '#8b5cf6';
    }
    $productCount = $department->products_count ?? 0;
    $isActive = ($currentSlug ?? '') === $department->slug;
@endphp

<a href="{{ route('category.show', $department->slug) }}"
   class="group w-full flex flex-col rounded-2xl border overflow-hidden transition-all duration-300
          {{ $isActive
              ? 'border-violet-500 shadow-lg ring-2 ring-violet-500/20'
              : 'border-slate-200 bg-white hover:border-violet-300 hover:shadow-md' }}">
    <div class="h-20 relative overflow-hidden"
         style="background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }});">
        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
        <div class="absolute bottom-3 right-3 h-10 w-10 rounded-xl bg-white shadow-md flex items-center justify-center p-1.5">
            @if($department->icon_url)
                <img src="{{ $department->icon_url }}" alt="" class="max-h-full max-w-full object-contain">
            @else
                <span class="text-xs font-black" style="color: {{ $primary }}">{{ strtoupper(substr($department->name, 0, 2)) }}</span>
            @endif
        </div>
    </div>
    <div class="p-4">
        <h3 class="font-bold text-slate-900 text-sm group-hover:text-violet-700 transition-colors">{{ $department->name }}</h3>
        @if($department->description)
            <p class="text-[11px] text-slate-500 mt-1 line-clamp-2">{{ $department->description }}</p>
        @endif
        <div class="mt-3 flex items-center justify-between text-[11px] font-bold">
            <span class="text-slate-500">{{ $productCount }} products</span>
            <span class="text-violet-600">View →</span>
        </div>
    </div>
</a>
