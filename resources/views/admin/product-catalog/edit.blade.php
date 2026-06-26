@extends('admin.settings.layout')

@section('settings-content')
<div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
    <div class="border-b border-slate-100 px-6 py-4">
        <h3 class="text-base font-bold text-slate-800">صفحة المنتجات</h3>
        <p class="text-xs text-slate-500 mt-1">تحكم في عنوان ووصف وSEO صفحة قائمة المنتجات.</p>
    </div>

    <form method="POST" action="{{ route('admin.product-catalog.update') }}" class="p-6 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500">عنوان الصفحة</label>
                <input type="text" name="catalog_page_title" value="{{ site_setting('catalog_page_title', 'All Products') }}" class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm">
            </div>
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500">SEO Title</label>
                <input type="text" name="catalog_seo_title" value="{{ site_setting('catalog_seo_title') }}" class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm">
            </div>
            <div class="space-y-2 md:col-span-2">
                <label class="text-xs font-bold text-slate-500">النص الفرعي / الوصف</label>
                <input type="text" name="catalog_page_subtitle" value="{{ site_setting('catalog_page_subtitle', 'Browse all available products') }}" class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm">
            </div>
            <div class="space-y-2 md:col-span-2">
                <label class="text-xs font-bold text-slate-500">SEO Description</label>
                <textarea name="catalog_seo_description" rows="3" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm">{{ site_setting('catalog_seo_description') }}</textarea>
            </div>
            <div class="space-y-2 md:col-span-2">
                <label class="text-xs font-bold text-slate-500">SEO Keywords</label>
                <input type="text" name="catalog_seo_keywords" value="{{ site_setting('catalog_seo_keywords') }}" class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm">
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t border-slate-100">
            <button type="submit" class="h-11 px-8 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl transition-colors shadow-lg shadow-emerald-500/20">
                <i class="fa-solid fa-floppy-disk ml-2"></i> حفظ التغييرات
            </button>
        </div>
    </form>
</div>
@endsection
