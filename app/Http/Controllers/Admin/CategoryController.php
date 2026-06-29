<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ImageService;
use App\Services\NavigationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(private ImageService $imageService)
    {
    }

    /* ──────────────── الكليات (تصنيفات رئيسية) ──────────────── */

    public function collegesIndex(): View
    {
        $colleges = Category::query()
            ->roots()
            ->withCount(['children', 'products'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.colleges.index', compact('colleges'));
    }

    public function collegesCreate(): View
    {
        return view('admin.colleges.form', [
            'category' => new Category(),
            'title' => 'إضافة كلية جديدة',
        ]);
    }

    public function collegesStore(Request $request): RedirectResponse
    {
        $validated = $this->validateCollege($request);
        $validated['parent_id'] = null;
        $validated['status'] = $request->boolean('status', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $this->handleCollegeImages($request, $validated);

        Category::create($validated);
        $this->clearCategoryCache();

        return redirect()->route('admin.colleges.index')->with('success', 'تم إضافة الكلية بنجاح.');
    }

    public function collegesEdit(Category $category): View|RedirectResponse
    {
        if ($category->parent_id !== null) {
            return redirect()->route('admin.subcategories.edit', $category);
        }

        return view('admin.colleges.form', [
            'category' => $category,
            'title' => 'تعديل الكلية: ' . $category->name,
        ]);
    }

    public function collegesUpdate(Request $request, Category $category): RedirectResponse
    {
        if ($category->parent_id !== null) {
            return redirect()->route('admin.subcategories.index')->with('error', 'هذا تصنيف فرعي وليس كلية.');
        }

        $validated = $this->validateCollege($request, $category->id);
        $validated['parent_id'] = null;
        $validated['status'] = $request->boolean('status', true);

        $this->handleCollegeImages($request, $validated, $category);

        $oldSlug = $category->slug;
        $category->update($validated);
        $this->clearCategoryCache($category, $oldSlug);

        return redirect()->route('admin.colleges.index')->with('success', 'تم تحديث الكلية بنجاح.');
    }

    public function collegesDestroy(Category $category): RedirectResponse
    {
        if ($category->parent_id !== null) {
            return redirect()->route('admin.colleges.index')->with('error', 'لا يمكن حذف تصنيف فرعي من هنا.');
        }

        if ($category->children()->exists()) {
            return redirect()->route('admin.colleges.index')
                ->with('error', 'لا يمكن حذف كلية تحتوي على تصنيفات فرعية.');
        }

        if ($category->products()->exists()) {
            return redirect()->route('admin.colleges.index')
                ->with('error', 'لا يمكن حذف كلية مرتبطة بمنتجات.');
        }

        $this->imageService->deletePaths($category->image, $category->banner);
        $category->delete();
        $this->clearCategoryCache($category);

        return redirect()->route('admin.colleges.index')->with('success', 'تم حذف الكلية بنجاح.');
    }

    /* ──────────────── التصنيفات الفرعية ──────────────── */

    public function subcategoriesIndex(Request $request): View
    {
        $collegeId = $request->get('college_id');

        $subcategories = Category::query()
            ->whereNotNull('parent_id')
            ->with('parent:id,name')
            ->withCount('products')
            ->when($collegeId, fn ($q) => $q->where('parent_id', $collegeId))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $colleges = Category::roots()->active()->orderBy('name')->get(['id', 'name']);

        return view('admin.subcategories.index', compact('subcategories', 'colleges', 'collegeId'));
    }

    public function subcategoriesCreate(): View
    {
        $colleges = Category::roots()->active()->orderBy('name')->get(['id', 'name']);

        return view('admin.subcategories.form', [
            'category' => new Category(),
            'colleges' => $colleges,
            'title' => 'إضافة تصنيف فرعي',
        ]);
    }

    public function subcategoriesStore(Request $request): RedirectResponse
    {
        $validated = $this->validateSubcategory($request);
        $validated['status'] = $request->boolean('status', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if ($request->hasFile('image')) {
            $validated['image'] = $this->imageService->storeCategoryImage($request->file('image'), 'image');
        }

        Category::create($validated);
        $this->clearCategoryCache();

        return redirect()->route('admin.subcategories.index')->with('success', 'تم إضافة التصنيف الفرعي بنجاح.');
    }

    public function subcategoriesEdit(Category $category): View|RedirectResponse
    {
        if ($category->parent_id === null) {
            return redirect()->route('admin.colleges.edit', $category);
        }

        $colleges = Category::roots()->active()->orderBy('name')->get(['id', 'name']);

        return view('admin.subcategories.form', [
            'category' => $category,
            'colleges' => $colleges,
            'title' => 'تعديل التصنيف: ' . $category->name,
        ]);
    }

    public function subcategoriesUpdate(Request $request, Category $category): RedirectResponse
    {
        if ($category->parent_id === null) {
            return redirect()->route('admin.colleges.index')->with('error', 'هذا تصنيف كلية وليس تصنيفاً فرعياً.');
        }

        $validated = $this->validateSubcategory($request, $category->id);
        $validated['status'] = $request->boolean('status', true);

        if ($request->hasFile('image')) {
            $this->imageService->deletePaths($category->image);
            $validated['image'] = $this->imageService->storeCategoryImage($request->file('image'), 'image');
        }

        $oldSlug = $category->slug;
        $category->update($validated);
        $this->clearCategoryCache($category, $oldSlug);

        return redirect()->route('admin.subcategories.index')->with('success', 'تم تحديث التصنيف الفرعي بنجاح.');
    }

    public function subcategoriesDestroy(Category $category): RedirectResponse
    {
        if ($category->parent_id === null) {
            return redirect()->route('admin.subcategories.index')->with('error', 'لا يمكن حذف كلية من هنا.');
        }

        if ($category->products()->exists()) {
            return redirect()->route('admin.subcategories.index')
                ->with('error', 'لا يمكن حذف تصنيف مرتبط بمنتجات.');
        }

        $this->imageService->deletePaths($category->image, $category->banner);
        $category->delete();
        $this->clearCategoryCache($category);

        return redirect()->route('admin.subcategories.index')->with('success', 'تم حذف التصنيف الفرعي بنجاح.');
    }

    /* ──────────────── API & Utilities ──────────────── */

    public function children(Category $category): JsonResponse
    {
        $children = $category->children()
            ->active()
            ->orderBy('sort_order')
            ->get(['id', 'name', 'parent_id']);

        return response()->json($children);
    }

    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:categories,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            Category::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        $this->clearCategoryCache();

        return response()->json(['success' => true]);
    }

    /* ──────────────── Validation ──────────────── */

    private function validateCollege(Request $request, ?int $ignoreId = null): array
    {
        $slugRule = 'nullable|string|max:255|unique:categories,slug';
        if ($ignoreId) {
            $slugRule .= ',' . $ignoreId;
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => $slugRule,
            'description' => 'nullable|string',
            'primary_color' => 'nullable|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:4096',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:6144',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|string',
            'canonical_url' => 'nullable|url|max:255',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string',
            'schema_markup' => 'nullable|string',
        ], [
            'name.required' => 'اسم الكلية مطلوب.',
            'image.image' => 'الأيقونة يجب أن تكون صورة.',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = arabic_slug($validated['name']) ?: Str::slug($validated['name']);
        }

        return $validated;
    }

    private function validateSubcategory(Request $request, ?int $ignoreId = null): array
    {
        $slugRule = 'nullable|string|max:255|unique:categories,slug';
        if ($ignoreId) {
            $slugRule .= ',' . $ignoreId;
        }

        $validated = $request->validate([
            'parent_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => $slugRule,
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|string',
            'canonical_url' => 'nullable|url|max:255',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string',
            'schema_markup' => 'nullable|string',
        ], [
            'name.required' => 'اسم التصنيف الفرعي مطلوب.',
            'parent_id.required' => 'يجب اختيار الكلية.',
        ]);

        if (empty($validated['slug'])) {
            $parent = Category::find($validated['parent_id']);
            $validated['slug'] = arabic_slug(($parent?->name ?? '') . '-' . $validated['name']) ?: Str::slug(($parent?->name ?? '') . '-' . $validated['name']);
        }

        return $validated;
    }

    private function handleCollegeImages(Request $request, array &$validated, ?Category $existing = null): void
    {
        if ($request->hasFile('image')) {
            if ($existing?->image) {
                $this->imageService->deletePaths($existing->image);
            }
            $validated['image'] = $this->imageService->storeCategoryImage($request->file('image'), 'image');
        }

        if ($request->hasFile('banner')) {
            if ($existing?->banner) {
                $this->imageService->deletePaths($existing->banner);
            }
            $validated['banner'] = $this->imageService->storeCategoryImage($request->file('banner'), 'banner');
        }
    }

    private function clearCategoryCache(?Category $category = null, ?string $oldSlug = null): void
    {
        NavigationService::clearCache();

        if ($oldSlug) {
            Cache::forget("category_page_{$oldSlug}");
        }

        if ($category?->slug) {
            Cache::forget("category_page_{$category->slug}");
        }
    }
}
