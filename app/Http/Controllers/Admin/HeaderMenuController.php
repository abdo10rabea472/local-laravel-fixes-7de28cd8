<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HeaderMenuItem;
use App\Services\NavigationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HeaderMenuController extends Controller
{
    private const LOCATIONS = [
        'header_primary' => 'القائمة الرئيسية',
        'header_top' => 'الشريط العلوي',
        'footer' => 'قائمة الفوتر',
    ];

    public function index(Request $request): View
    {
        $location = $request->get('location', 'header_primary');
        if (! array_key_exists($location, self::LOCATIONS)) {
            $location = 'header_primary';
        }

        $items = HeaderMenuItem::root()
            ->byLocation($location)
            ->with(['children' => fn ($q) => $q->active()->orderBy('position')])
            ->orderBy('position')
            ->paginate(50);

        $categories = Category::roots()->active()->orderBy('name')->get(['id', 'name', 'slug']);

        return view('admin.settings.header-menu', [
            'items' => $items,
            'categories' => $categories,
            'activeTab' => 'header-menu',
            'locations' => self::LOCATIONS,
            'currentLocation' => $location,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateItem($request);
        $validated['status'] = $request->boolean('status', true);
        $validated['position'] = $validated['position'] ?? 0;
        $validated['location'] = $validated['location'] ?? 'header_primary';

        HeaderMenuItem::create($validated);
        NavigationService::clearCache();

        return redirect()->route('admin.settings.header-menu', ['location' => $validated['location']])->with('success', 'تم إضافة عنصر القائمة بنجاح.');
    }

    public function update(Request $request, HeaderMenuItem $item): RedirectResponse
    {
        $validated = $this->validateItem($request, $item->id);
        $validated['status'] = $request->boolean('status', true);

        $item->update($validated);
        NavigationService::clearCache();

        return redirect()->route('admin.settings.header-menu', ['location' => $item->location])->with('success', 'تم تحديث عنصر القائمة بنجاح.');
    }

    public function destroy(HeaderMenuItem $item): RedirectResponse
    {
        $location = $item->location;
        $item->delete();
        NavigationService::clearCache();

        return redirect()->route('admin.settings.header-menu', ['location' => $location])->with('success', 'تم حذف عنصر القائمة بنجاح.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:header_menu_items,id',
            'items.*.position' => 'required|integer|min:0',
            'items.*.parent_id' => 'nullable|exists:header_menu_items,id',
        ]);

        foreach ($request->items as $data) {
            HeaderMenuItem::where('id', $data['id'])->update([
                'position' => $data['position'],
                'parent_id' => $data['parent_id'] ?? null,
            ]);
        }

        NavigationService::clearCache();

        return response()->json(['success' => true]);
    }

    private function validateItem(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'parent_id' => 'nullable|exists:header_menu_items,id',
            'title' => 'required|string|max:255',
            'url' => 'nullable|string|max:500',
            'type' => 'nullable|in:link,coupon',
            'coupon_code' => 'nullable|string|max:100',
            'coupon_percent' => 'nullable|integer|min:0|max:100',
            'icon' => 'nullable|string|max:100',
            'target' => 'nullable|in:_self,_blank',
            'position' => 'nullable|integer|min:0',
            'location' => 'nullable|in:header_primary,header_top,footer',
        ], [
            'title.required' => 'عنوان العنصر مطلوب.',
        ]);
    }
}
