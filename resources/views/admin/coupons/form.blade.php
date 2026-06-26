@extends('admin.layouts.app')
@section('title', $coupon->exists ? 'تعديل كود خصم' : 'إنشاء كود خصم')

@section('content')
<div class="p-6 max-w-3xl mx-auto" x-data="{ scope: '{{ old('scope', $coupon->scope ?? 'all') }}' }">
    @if($errors->any())
        <div class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl">
            @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
    @endif

    <h1 class="text-2xl font-black text-slate-900 mb-6">{{ $coupon->exists ? 'تعديل كود الخصم' : 'إنشاء كود خصم جديد' }}</h1>

    <form method="POST" action="{{ $coupon->exists ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}" class="bg-white rounded-3xl border border-slate-200 p-6 space-y-5">
        @csrf
        @if($coupon->exists) @method('PUT') @endif

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-bold text-slate-500 block mb-1">كود الخصم</label>
                <input type="text" name="code" value="{{ old('code', $coupon->code) }}" required class="w-full h-11 px-3 border border-slate-200 rounded-xl text-sm uppercase font-mono">
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500 block mb-1">حالة الكود</label>
                <label class="inline-flex items-center gap-2 mt-2">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $coupon->is_active ?? true))> مفعل
                </label>
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-bold text-slate-500 block mb-1">نوع الخصم</label>
                <select name="type" class="w-full h-11 px-3 border border-slate-200 rounded-xl text-sm">
                    <option value="percent" @selected(old('type', $coupon->type)==='percent')>نسبة %</option>
                    <option value="fixed" @selected(old('type', $coupon->type)==='fixed')>مبلغ ثابت</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500 block mb-1">قيمة الخصم</label>
                <input type="number" step="0.01" min="0" name="value" value="{{ old('value', $coupon->value) }}" required class="w-full h-11 px-3 border border-slate-200 rounded-xl text-sm">
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-bold text-slate-500 block mb-1">تاريخ البداية (اختياري)</label>
                <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d\TH:i')) }}" class="w-full h-11 px-3 border border-slate-200 rounded-xl text-sm">
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500 block mb-1">تاريخ الانتهاء (اختياري)</label>
                <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $coupon->ends_at?->format('Y-m-d\TH:i')) }}" class="w-full h-11 px-3 border border-slate-200 rounded-xl text-sm">
            </div>
        </div>

        <div class="grid sm:grid-cols-3 gap-4">
            <div>
                <label class="text-xs font-bold text-slate-500 block mb-1">الحد الأدنى للطلب</label>
                <input type="number" step="0.01" min="0" name="min_order_total" value="{{ old('min_order_total', $coupon->min_order_total) }}" class="w-full h-11 px-3 border border-slate-200 rounded-xl text-sm">
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500 block mb-1">الحد الأقصى للخصم</label>
                <input type="number" step="0.01" min="0" name="max_discount_amount" value="{{ old('max_discount_amount', $coupon->max_discount_amount) }}" class="w-full h-11 px-3 border border-slate-200 rounded-xl text-sm">
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500 block mb-1">الحد الأقصى للاستخدام</label>
                <input type="number" min="1" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}" class="w-full h-11 px-3 border border-slate-200 rounded-xl text-sm">
            </div>
        </div>

        <div>
            <label class="text-xs font-bold text-slate-500 block mb-1">نطاق الكود</label>
            <select name="scope" x-model="scope" class="w-full h-11 px-3 border border-slate-200 rounded-xl text-sm">
                <option value="all">جميع المنتجات</option>
                <option value="products">منتجات محددة</option>
                <option value="categories">أقسام محددة</option>
            </select>
        </div>

        <div x-show="scope === 'products'" x-cloak>
            <label class="text-xs font-bold text-slate-500 block mb-1">اختر المنتجات</label>
            <select name="product_ids[]" multiple size="8" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm">
                @foreach($products as $p)
                    <option value="{{ $p->id }}" @selected(in_array($p->id, old('product_ids', $selectedProducts)))>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>

        <div x-show="scope === 'categories'" x-cloak>
            <label class="text-xs font-bold text-slate-500 block mb-1">اختر الأقسام</label>
            <select name="category_ids[]" multiple size="8" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm">
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" @selected(in_array($c->id, old('category_ids', $selectedCategories)))>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-xs font-bold text-slate-500 block mb-1">رسالة وصفية (اختياري)</label>
            <textarea name="description" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm">{{ old('description', $coupon->description) }}</textarea>
        </div>

        <div class="flex gap-2 pt-3 border-t border-slate-100">
            <button class="px-6 py-2.5 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-xl">حفظ</button>
            <a href="{{ route('admin.coupons.index') }}" class="px-6 py-2.5 bg-slate-100 text-slate-700 font-bold rounded-xl">إلغاء</a>
        </div>
    </form>
</div>
@endsection
