@extends('admin.layouts.app')

@section('title', $title)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold text-slate-800">{{ $title }}</h3>
            <p class="text-xs text-slate-500 mt-1">اسم الكلية، الأيقونة، ألوان الصفحة، الوصف، وSEO</p>
        </div>
        <a href="{{ route('admin.colleges.index') }}" class="text-sm font-bold text-violet-600 hover:text-violet-800">
            <i class="fa-solid fa-arrow-right ml-1"></i> العودة للكليات
        </a>
    </div>

    <form
        method="POST"
        action="{{ $category->exists ? route('admin.colleges.update', $category) : route('admin.colleges.store') }}"
        enctype="multipart/form-data"
        class="bg-white border border-slate-200 rounded-3xl shadow-sm p-6 space-y-6"
    >
        @csrf
        @if($category->exists) @method('PUT') @endif

        {{-- البيانات الأساسية --}}
        <div>
            <h4 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-building-columns text-violet-600"></i>
                بيانات الكلية
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-slate-500">اسم الكلية *</label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                        placeholder="مثال: Medicine, Engineering..."
                        class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm mt-1 focus:border-violet-400 outline-none">
                    @error('name')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500">Slug (رابط الصفحة)</label>
                    <input type="text" name="slug" value="{{ old('slug', $category->slug) }}"
                        placeholder="يُولَّد تلقائياً من الاسم"
                        class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm mt-1 focus:border-violet-400 outline-none">
                </div>
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
                        الكلية نشطة وظاهرة في المتجر
                    </label>
                </div>
            </div>
        </div>

        {{-- الأيقونة والبانر --}}
        <div class="border-t border-slate-100 pt-6">
            <h4 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-image text-violet-600"></i>
                الأيقونة والبانر
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-xs font-bold text-slate-500">أيقونة الكلية</label>
                    <p class="text-[11px] text-slate-400 mb-2">تظهر في قائمة الكليات والصفحة الرئيسية</p>
                    @if($category->image)
                    <div class="mb-3 p-3 bg-slate-50 rounded-2xl inline-block">
                        <img src="{{ asset('storage/' . $category->image) }}" alt="" class="h-16 w-16 object-contain">
                    </div>
                    @endif
                    <input type="file" name="image" accept="image/*"
                        class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-violet-50 file:text-violet-700 file:font-bold">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500">بانر صفحة الكلية</label>
                    <p class="text-[11px] text-slate-400 mb-2">صورة عريضة أعلى صفحة الكلية</p>
                    @if($category->banner)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $category->banner) }}" alt="" class="h-20 rounded-2xl object-cover">
                    </div>
                    @endif
                    <input type="file" name="banner" accept="image/*"
                        class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-violet-50 file:text-violet-700 file:font-bold">
                </div>
            </div>
        </div>

        {{-- ألوان الصفحة --}}
        <div class="border-t border-slate-100 pt-6">
            <h4 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-palette text-violet-600"></i>
                ألوان صفحة الكلية
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-slate-500">اللون الأساسي</label>
                    <div class="flex items-center gap-3 mt-1">
                        <input type="color" name="primary_color"
                            value="{{ old('primary_color', $category->primary_color ?? '#6366f1') }}"
                            class="h-11 w-16 rounded-xl border border-slate-200 cursor-pointer">
                        <input type="text" value="{{ old('primary_color', $category->primary_color ?? '#6366f1') }}"
                            oninput="this.previousElementSibling.value = this.value"
                            onchange="document.querySelector('[name=primary_color]').value = this.value"
                            class="flex-1 h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-mono">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500">اللون الثانوي</label>
                    <div class="flex items-center gap-3 mt-1">
                        <input type="color" name="secondary_color"
                            value="{{ old('secondary_color', $category->secondary_color ?? '#8b5cf6') }}"
                            class="h-11 w-16 rounded-xl border border-slate-200 cursor-pointer">
                        <input type="text" value="{{ old('secondary_color', $category->secondary_color ?? '#8b5cf6') }}"
                            oninput="this.previousElementSibling.value = this.value"
                            class="flex-1 h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-mono">
                    </div>
                </div>
            </div>
            {{-- Preview --}}
            <div class="mt-4 p-4 rounded-2xl border border-slate-200"
                style="background: linear-gradient(135deg, {{ old('primary_color', $category->primary_color ?? '#6366f1') }}, {{ old('secondary_color', $category->secondary_color ?? '#8b5cf6') }})">
                <p class="text-white font-bold text-sm">معاينة ألوان صفحة الكلية</p>
            </div>
        </div>

        {{-- الوصف --}}
        <div class="border-t border-slate-100 pt-6">
            <h4 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
                <i class="fa-solid fa-align-right text-violet-600"></i>
                وصف صفحة الكلية
            </h4>
            <textarea name="description" rows="4"
                placeholder="وصف مختصر عن الكلية ومنتجاتها..."
                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm outline-none focus:border-violet-400">{{ old('description', $category->description) }}</textarea>
        </div>

        {{-- SEO --}}
        <div class="border-t border-slate-100 pt-6">
            <h4 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-magnifying-glass-chart text-violet-600"></i>
                إعدادات SEO
            </h4>
            <div class="space-y-3">
                <div>
                    <label class="text-xs font-bold text-slate-500">SEO Title</label>
                    <input type="text" name="seo_title" value="{{ old('seo_title', $category->exists ? $category->getRawOriginal('seo_title') : '') }}"
                        placeholder="عنوان يظهر في محركات البحث"
                        class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm mt-1 outline-none">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500">Meta Description</label>
                    <textarea name="seo_description" rows="2"
                        placeholder="وصف الصفحة في نتائج البحث (160 حرف)"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm outline-none">{{ old('seo_description', $category->exists ? $category->getRawOriginal('seo_description') : '') }}</textarea>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500">Keywords</label>
                    <input type="text" name="seo_keywords" value="{{ old('seo_keywords', $category->seo_keywords) }}"
                        placeholder="medicine, medical tools, stethoscope"
                        class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm mt-1 outline-none">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500">Canonical URL</label>
                    <input type="url" name="canonical_url" value="{{ old('canonical_url', $category->canonical_url) }}"
                        placeholder="https://..."
                        class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm mt-1 outline-none">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-bold text-slate-500">OG Title</label>
                        <input type="text" name="og_title" value="{{ old('og_title', $category->og_title) }}"
                            class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm mt-1 outline-none">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500">OG Description</label>
                        <input type="text" name="og_description" value="{{ old('og_description', $category->og_description) }}"
                            class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm mt-1 outline-none">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500">Schema JSON-LD</label>
                    <textarea name="schema_markup" rows="3"
                        placeholder='{"@@context": "https://schema.org", ...}'
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-mono outline-none">{{ old('schema_markup', $category->schema_markup) }}</textarea>
                </div>
            </div>
        </div>

        <button type="submit"
            class="w-full h-12 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 text-white font-bold rounded-2xl shadow-lg shadow-violet-500/20 transition-all">
            <i class="fa-solid fa-floppy-disk ml-2"></i>
            {{ $category->exists ? 'حفظ التعديلات' : 'إضافة الكلية' }}
        </button>
    </form>
</div>
@endsection
