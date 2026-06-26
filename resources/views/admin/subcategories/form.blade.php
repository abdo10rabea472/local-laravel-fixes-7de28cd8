@extends('admin.layouts.app')

@section('title', $title)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold text-slate-800">{{ $title }}</h3>
            <p class="text-xs text-slate-500 mt-1">تصنيف فرعي تابع لكلية معينة</p>
        </div>
        <a href="{{ route('admin.subcategories.index') }}" class="text-sm font-bold text-violet-600 hover:text-violet-800">
            <i class="fa-solid fa-arrow-right ml-1"></i> العودة للتصنيفات الفرعية
        </a>
    </div>

    <form
        method="POST"
        action="{{ $category->exists ? route('admin.subcategories.update', $category) : route('admin.subcategories.store') }}"
        enctype="multipart/form-data"
        class="bg-white border border-slate-200 rounded-3xl shadow-sm p-6 space-y-6"
    >
        @csrf
        @if($category->exists) @method('PUT') @endif

        <div>
            <h4 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-sitemap text-indigo-600"></i>
                بيانات التصنيف الفرعي
            </h4>
            <div class="space-y-4">
                <div>
                    <label class="text-xs font-bold text-slate-500">الكلية *</label>
                    <select name="parent_id" required
                        class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm mt-1 outline-none focus:border-violet-400">
                        <option value="">— اختر الكلية —</option>
                        @foreach($colleges as $college)
                            <option value="{{ $college->id }}"
                                @selected(old('parent_id', $category->parent_id) == $college->id)>
                                {{ $college->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500">اسم التصنيف *</label>
                        <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                            placeholder="مثال: Clinical Tools, Electrical Engineering..."
                            class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm mt-1 outline-none">
                        @error('name')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500">Slug</label>
                        <input type="text" name="slug" value="{{ old('slug', $category->slug) }}"
                            class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm mt-1 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500">ترتيب العرض</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}"
                            class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm mt-1 outline-none">
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-2 text-sm font-semibold cursor-pointer">
                            <input type="checkbox" name="status" value="1"
                                @checked(old('status', $category->exists ? $category->status : true))
                                class="rounded text-violet-600">
                            التصنيف نشط
                        </label>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-500">صورة التصنيف (اختياري)</label>
                    @if($category->image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $category->image) }}" class="h-16 rounded-xl object-cover" alt="">
                    </div>
                    @endif
                    <input type="file" name="image" accept="image/*"
                        class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-bold">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-500">وصف التصنيف</label>
                    <textarea name="description" rows="3"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm outline-none">{{ old('description', $category->description) }}</textarea>
                </div>
            </div>
        </div>

        <div class="border-t border-slate-100 pt-6">
            <h4 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-magnifying-glass-chart text-indigo-600"></i>
                SEO
            </h4>
            <div class="space-y-3">
                <input type="text" name="seo_title" value="{{ old('seo_title', $category->exists ? $category->getRawOriginal('seo_title') : '') }}"
                    placeholder="SEO Title"
                    class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm outline-none">
                <textarea name="seo_description" rows="2" placeholder="Meta Description"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm outline-none">{{ old('seo_description', $category->exists ? $category->getRawOriginal('seo_description') : '') }}</textarea>
                <input type="text" name="seo_keywords" value="{{ old('seo_keywords', $category->seo_keywords) }}"
                    placeholder="Keywords"
                    class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm outline-none">
                <input type="url" name="canonical_url" value="{{ old('canonical_url', $category->canonical_url) }}"
                    placeholder="Canonical URL"
                    class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm outline-none">
            </div>
        </div>

        <button type="submit"
            class="w-full h-12 bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-bold rounded-2xl shadow-lg">
            <i class="fa-solid fa-floppy-disk ml-2"></i>
            {{ $category->exists ? 'حفظ التعديلات' : 'إضافة التصنيف الفرعي' }}
        </button>
    </form>
</div>
@endsection
