@extends('admin.layouts.app')

@section('title', $product->exists ? 'تعديل منتج' : 'إضافة منتج جديد')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold text-slate-800">{{ $product->exists ? 'تعديل منتج' : 'إضافة منتج جديد' }}</h3>
            <p class="text-xs text-slate-500 mt-1">اختر التصنيف الفرعي للكلية، ثم أدخل بيانات المنتج والصور وSEO</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-sm font-bold text-violet-600 hover:text-violet-800">
            <i class="fa-solid fa-arrow-right ml-1"></i> العودة للقائمة
        </a>
    </div>

    <form
        method="POST"
        action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}"
        enctype="multipart/form-data"
        class="bg-white border border-slate-200 rounded-3xl shadow-sm p-6 space-y-6"
    >
        @csrf
        @if($product->exists) @method('PUT') @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-bold text-slate-500">اسم المنتج *</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm">
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500">Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $product->slug) }}" class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm">
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500">SKU</label>
                <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm">
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500">التصنيف *</label>
                <select name="category_id" id="category_id" required class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm">
                    <option value="">اختر التصنيف</option>
                    @foreach($categories->whereNull('parent_id') as $parent)
                        <optgroup label="{{ $parent->name }}">
                            @foreach($categories->where('parent_id', $parent->id) as $child)
                                <option value="{{ $child->id }}" @selected(old('category_id', $product->category_id) == $child->id)>{{ $child->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500">السعر *</label>
                <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" required class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm">
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500">سعر التخفيض</label>
                <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm">
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500">المخزون *</label>
                <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" required class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm">
            </div>
        </div>

        <div>
            <label class="text-xs font-bold text-slate-500">وصف مختصر</label>
            <textarea name="short_description" rows="2" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm">{{ old('short_description', $product->short_description) }}</textarea>
        </div>
        <div>
            <label class="text-xs font-bold text-slate-500">الوصف الكامل</label>
            <textarea name="description" rows="5" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm">{{ old('description', $product->description) }}</textarea>
        </div>

        <div>
            @php $currentCount = $product->exists ? $product->images->count() : 0; $remaining = max(0, 5 - $currentCount); @endphp
            <label class="text-xs font-bold text-slate-500">
                صور المنتج (الحد الأدنى 4 — الحد الأقصى 5 — متبقي {{ $remaining }})
            </label>
            <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp,image/gif"
                   class="w-full text-sm mt-1" @if($remaining === 0) disabled @endif>
            <p class="text-[11px] text-slate-400 mt-1">يجب رفع 4 صور على الأقل لكل منتج. سيتم تحويل الصور تلقائيًا إلى WebP لتقليل الحجم. الحد الأقصى 4MB للصورة.</p>
            @error('images') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            @error('images.*') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            @if($product->exists && $product->images->count())
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4">
                @foreach($product->images as $image)
                <label class="relative border border-slate-200 rounded-2xl overflow-hidden">
                    <img src="{{ $image->getUrl('thumb') }}" alt="" class="w-full h-28 object-cover">
                    <div class="p-2 text-xs">
                        <input type="checkbox" name="remove_images[]" value="{{ $image->id }}"> حذف
                    </div>
                </label>
                @endforeach
            </div>
            @endif
        </div>

        <div class="border-t border-slate-100 pt-4 space-y-3">
            <h4 class="font-bold text-sm">SEO</h4>
            <input type="text" name="seo_title" value="{{ old('seo_title', $product->exists ? $product->getRawOriginal('seo_title') : '') }}" placeholder="SEO Title" class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm">
            <textarea name="seo_description" rows="2" placeholder="Meta Description" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm">{{ old('seo_description', $product->exists ? $product->getRawOriginal('seo_description') : '') }}</textarea>
            <input type="text" name="seo_keywords" value="{{ old('seo_keywords', $product->seo_keywords) }}" placeholder="Keywords" class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm">
            <input type="url" name="canonical_url" value="{{ old('canonical_url', $product->canonical_url) }}" placeholder="Canonical URL" class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm">
            <textarea name="schema_markup" rows="4" placeholder="Schema JSON-LD" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-mono">{{ old('schema_markup', $product->schema_markup) }}</textarea>
        </div>

        <div class="flex gap-6">
            <label class="flex items-center gap-2 text-sm font-semibold">
                <input type="checkbox" name="featured" value="1" @checked(old('featured', $product->featured)) class="rounded"> منتج مميز
            </label>
            <label class="flex items-center gap-2 text-sm font-semibold">
                <input type="checkbox" name="status" value="1" @checked(old('status', $product->exists ? $product->status : true)) class="rounded"> نشط
            </label>
        </div>

        <button type="submit" class="w-full h-12 bg-gradient-to-r from-violet-600 to-indigo-600 text-white font-bold rounded-2xl shadow-lg">
            {{ $product->exists ? 'تحديث المنتج' : 'حفظ المنتج' }}
        </button>
    </form>
</div>
@endsection
