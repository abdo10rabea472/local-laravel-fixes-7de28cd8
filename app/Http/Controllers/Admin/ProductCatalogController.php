<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductCatalogController extends Controller
{
    public function edit(): View
    {
        $settings = SiteSetting::whereIn('key', [
            'catalog_page_title',
            'catalog_page_subtitle',
            'catalog_seo_title',
            'catalog_seo_description',
            'catalog_seo_keywords',
        ])->get()->keyBy('key');

        return view('admin.product-catalog.edit', compact('settings') + ['activeTab' => 'product-catalog']);
    }

    public function update(Request $request): RedirectResponse
    {
        $keys = [
            'catalog_page_title',
            'catalog_page_subtitle',
            'catalog_seo_title',
            'catalog_seo_description',
            'catalog_seo_keywords',
        ];

        foreach ($keys as $key) {
            $setting = SiteSetting::firstOrNew(['key' => $key]);
            $setting->value = $request->input($key);
            $setting->type = str_contains($key, 'description') ? 'textarea' : 'text';
            $setting->group = 'product_catalog';
            $setting->label = $setting->label ?: $this->label($key);
            $setting->save();
        }

        \App\Models\SiteSetting::clearCache();

        return redirect()->route('admin.product-catalog.edit')->with('success', 'تم تحديث صفحة المنتجات بنجاح.');
    }

    private function label(string $key): string
    {
        return match ($key) {
            'catalog_page_title' => 'عنوان الصفحة',
            'catalog_page_subtitle' => 'نص الصفحة الفرعي',
            'catalog_seo_title' => 'SEO Title',
            'catalog_seo_description' => 'SEO Description',
            'catalog_seo_keywords' => 'SEO Keywords',
            default => $key,
        };
    }
}
