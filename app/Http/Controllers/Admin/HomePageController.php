<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomePageController extends Controller
{
    public function __construct(private ImageService $imageService)
    {
    }

    public function edit(): View
    {
        $settings = SiteSetting::whereIn('key', [
            'hero_title',
            'hero_subtitle',
            'hero_badge',
            'hero_background',
            'featured_section_title',
            'featured_section_subtitle',
            'products_section_title',
            'products_section_subtitle',
        ])->get()->keyBy('key');

        return view('admin.homepage.edit', compact('settings') + ['activeTab' => 'homepage']);
    }

    public function update(Request $request): RedirectResponse
    {
        $keys = [
            'hero_title',
            'hero_subtitle',
            'hero_badge',
            'featured_section_title',
            'featured_section_subtitle',
            'featured_limit',
            'products_section_title',
            'products_section_subtitle',
            'products_limit',
        ];

        foreach ($keys as $key) {
            $setting = SiteSetting::firstOrNew(['key' => $key]);
            $setting->value = $request->input($key);
            $setting->type = 'text';
            $setting->group = 'homepage';
            $setting->label = $setting->label ?: $this->label($key);
            $setting->save();
        }

        if ($request->hasFile('hero_background')) {
            $setting = SiteSetting::firstOrNew(['key' => 'hero_background']);
            if ($setting->value) {
                $this->imageService->deletePaths($setting->value);
            }
            $setting->value = $this->imageService->storeSettingImage($request->file('hero_background'), 'hero_background');
            $setting->type = 'image';
            $setting->group = 'homepage';
            $setting->label = $setting->label ?: 'خلفية الصفحة الرئيسية';
            $setting->save();
        } elseif ($request->has('remove_hero_background')) {
            $setting = SiteSetting::firstOrNew(['key' => 'hero_background']);
            if ($setting->value) {
                $this->imageService->deletePaths($setting->value);
            }
            $setting->value = null;
            $setting->save();
        }

        SiteSetting::clearCache();

        return redirect()->route('admin.homepage.edit')->with('success', 'تم تحديث الصفحة الرئيسية بنجاح.');
    }

    private function label(string $key): string
    {
        return match ($key) {
            'hero_title' => 'عنوان Hero',
            'hero_subtitle' => 'نص Hero الفرعي',
            'hero_badge' => 'شارة Hero',
            'featured_section_title' => 'عنوان قسم المنتجات المميزة',
            'featured_section_subtitle' => 'نص قسم المنتجات المميزة',
            'featured_limit' => 'عدد المنتجات المميزة',
            'products_section_title' => 'عنوان قسم جميع المنتجات',
            'products_section_subtitle' => 'نص قسم جميع المنتجات',
            'products_limit' => 'عدد المنتجات في الصفحة',
            default => $key,
        };
    }
}
