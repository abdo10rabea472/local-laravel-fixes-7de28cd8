<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingRate;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShippingRateController extends Controller
{
    public function index(): View
    {
        $rates = ShippingRate::orderBy('position')->orderBy('state')->orderBy('city')->paginate(50);
        $freeThreshold = SiteSetting::get('free_shipping_threshold', '2000');

        return view('admin.settings.shipping', compact('rates', 'freeThreshold') + ['activeTab' => 'shipping']);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'country' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'city' => 'nullable|string|max:100',
            'cost' => 'required|numeric|min:0',
            'position' => 'nullable|integer|min:0',
        ]);

        $validated['status'] = $request->boolean('status', true);
        $validated['position'] = $validated['position'] ?? 0;

        ShippingRate::create($validated);
        SiteSetting::clearCache();

        return redirect()->route('admin.settings.shipping')->with('success', 'تم إضافة سعر الشحن بنجاح.');
    }

    public function update(Request $request, ShippingRate $rate): RedirectResponse
    {
        $validated = $request->validate([
            'country' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'city' => 'nullable|string|max:100',
            'cost' => 'required|numeric|min:0',
            'position' => 'nullable|integer|min:0',
        ]);

        $validated['status'] = $request->boolean('status', true);

        $rate->update($validated);
        SiteSetting::clearCache();

        return redirect()->route('admin.settings.shipping')->with('success', 'تم تحديث سعر الشحن بنجاح.');
    }

    public function destroy(ShippingRate $rate): RedirectResponse
    {
        $rate->delete();
        SiteSetting::clearCache();

        return redirect()->route('admin.settings.shipping')->with('success', 'تم حذف سعر الشحن بنجاح.');
    }

    public function updateThreshold(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'free_shipping_threshold' => 'required|numeric|min:0',
        ]);

        $setting = SiteSetting::firstOrNew(['key' => 'free_shipping_threshold']);
        $setting->value = $validated['free_shipping_threshold'];
        $setting->label = $setting->label ?: 'حد الشحن المجاني';
        $setting->group = 'shipping';
        $setting->save();
        SiteSetting::clearCache();

        return redirect()->route('admin.settings.shipping')->with('success', 'تم تحديث إعدادات الشحن بنجاح.');
    }
}
