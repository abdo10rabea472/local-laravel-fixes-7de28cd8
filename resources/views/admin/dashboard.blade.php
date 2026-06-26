@extends('admin.layouts.app')

@section('title', 'لوحة الإحصائيات العامة')

@section('content')
<div class="space-y-8">

    <!-- Overview Stats cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- card 1: Total Products -->
        <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm hover:shadow-md transition-all flex items-center justify-between">
            <div class="space-y-2">
                <span class="text-sm font-bold text-slate-500">إجمالي المنتجات</span>
                <h3 class="text-3xl font-black text-slate-800 font-mono">{{ $totalProducts }}</h3>
            </div>
            <div class="h-14 w-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl">
                <i class="fa-solid fa-box-open"></i>
            </div>
        </div>

        <!-- card 2: Total Stock Qty -->
        <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm hover:shadow-md transition-all flex items-center justify-between">
            <div class="space-y-2">
                <span class="text-sm font-bold text-slate-500">كميات المخزون</span>
                <h3 class="text-3xl font-black text-slate-800 font-mono">{{ $totalStock }}</h3>
            </div>
            <div class="h-14 w-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
        </div>

        <!-- card 3: Total Stock Value -->
        <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm hover:shadow-md transition-all flex items-center justify-between">
            <div class="space-y-2">
                <span class="text-sm font-bold text-slate-500">إجمالي قيمة المخزون</span>
                <h3 class="text-2xl font-black text-slate-800 font-mono">
                    {{ number_format($totalStockValue, 2) }}
                    <span class="text-xs font-bold text-emerald-600">ج.م</span>
                </h3>
            </div>
            <div class="h-14 w-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
        </div>

        <!-- card 4: Out of stock -->
        <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm hover:shadow-md transition-all flex items-center justify-between">
            <div class="space-y-2">
                <span class="text-sm font-bold text-slate-500">منتجات غير متوفرة</span>
                <h3 class="text-3xl font-black text-slate-800 font-mono {{ $outOfStockCount > 0 ? 'text-rose-600 animate-pulse' : '' }}">
                    {{ $outOfStockCount }}
                </h3>
            </div>
            <div class="h-14 w-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
        </div>

    </div>

    <!-- Middle Section: College statistics & Info -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Colleges stats list -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm col-span-1 space-y-6">
            <div>
                <h3 class="text-base font-bold text-slate-800">المنتجات حسب التصنيف</h3>
                <p class="text-xs text-slate-500 mt-1">إجمالي التصنيفات: {{ $totalCategories }}</p>
            </div>

            <div class="space-y-4">
                @forelse($categoryStats as $stat)
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-semibold text-slate-700">{{ $stat->category_name }}</span>
                        <span class="font-bold text-slate-900 font-mono">{{ $stat->count }}</span>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                        @php
                            $percentage = $totalProducts > 0 ? ($stat->count / $totalProducts) * 100 : 0;
                        @endphp
                        <div class="bg-violet-600 h-2 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
                @empty
                <div class="text-center py-6 text-slate-400 text-sm">لا توجد إحصائيات تصنيفات بعد.</div>
                @endforelse
            </div>
        </div>

        <!-- Quick actions & system state -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm col-span-1 lg:col-span-2 space-y-6 flex flex-col justify-between">
            <div class="space-y-2">
                <h3 class="text-base font-bold text-slate-800">إجراءات سريعة للأدمن</h3>
                <p class="text-xs text-slate-500">قم بإدارة متجرك والعمليات بكفاءة عالية</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a 
                    href="{{ route('admin.products.create') }}" 
                    class="p-5 border border-slate-100 hover:border-violet-100 hover:bg-violet-50/30 rounded-2xl flex items-center gap-4 transition-all group"
                >
                    <div class="h-12 w-12 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center text-xl group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-circle-plus"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-800">إضافة منتج جديد</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">أدخل تفاصيل وصور المنتجات</p>
                    </div>
                </a>

                <a 
                    href="{{ route('admin.colleges.create') }}" 
                    class="p-5 border border-slate-100 hover:border-indigo-100 hover:bg-indigo-50/30 rounded-2xl flex items-center gap-4 transition-all group"
                >
                    <div class="h-12 w-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-800">إضافة كلية</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">اسم، أيقونة، ألوان، وSEO</p>
                    </div>
                </a>
            </div>

            <!-- Dashboard Message Info Banner -->
            <div class="bg-gradient-to-r from-violet-600 to-indigo-600 text-white rounded-2xl p-5 shadow-lg flex items-center gap-4 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
                <div class="relative z-10 space-y-1">
                    <h4 class="text-sm font-bold">مرحبا بك في لوحة تحكم UNI-LAB MARKET</h4>
                    <p class="text-xs text-white/80 leading-relaxed">
                        بإمكانك إضافة المنتجات وتعديل أسعارها ونسب الخصم والكلية لتعرض تلقائياً لعملائك بالواجهة الرئيسية بشكل فوري وجمالي.
                    </p>
                </div>
            </div>
        </div>

    </div>

    <!-- Recent Products Table -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-base font-bold text-slate-800">آخر المنتجات المضافة</h3>
                <p class="text-xs text-slate-500 mt-1">قائمة بأحدث 5 منتجات تم تسجيلها بالنظام</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="text-xs font-bold text-violet-600 hover:text-violet-800 flex items-center gap-1">
                <span>عرض الكل</span>
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="text-slate-400 text-xs border-b border-slate-100">
                        <th class="pb-3 font-bold">المنتج</th>
                        <th class="pb-3 font-bold">التصنيف</th>
                        <th class="pb-3 font-bold">السعر</th>
                        <th class="pb-3 font-bold">المخزون</th>
                        <th class="pb-3 font-bold">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($recentProducts as $product)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4">
                            <span class="font-bold text-slate-800 block">{{ $product->name }}</span>
                        </td>
                        <td class="py-4 text-slate-600">{{ $product->category?->name ?? '—' }}</td>
                        <td class="py-4 font-mono font-bold text-slate-800">{{ number_format($product->sale_price ?? $product->price, 2) }} ج.م</td>
                        <td class="py-4 font-mono font-bold text-slate-700">{{ $product->stock }}</td>
                        <td class="py-4">
                            @if($product->stock > 0)
                            <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">متوفر</span>
                            @else
                            <span class="text-xs font-bold text-rose-700 bg-rose-50 px-2.5 py-1 rounded-full border border-rose-200">نافذ</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-8 text-slate-400">لا توجد منتجات مضافة بعد.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
