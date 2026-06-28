<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogCategoryController extends Controller
{
    public function index()
    {
        $categories = BlogCategory::withCount('posts')->orderBy('name')->paginate(20);
        return view('admin.content.blog-categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        BlogCategory::create($request->validate([
            'name' => ['required','string','max:255'],
            'slug' => ['nullable','string','max:255','unique:blog_categories,slug'],
            'description' => ['nullable','string','max:500'],
        ]));
        return back()->with('success', 'تم الإضافة.');
    }

    public function update(Request $request, BlogCategory $category)
    {
        $category->update($request->validate([
            'name' => ['required','string','max:255'],
            'slug' => ['nullable','string','max:255','unique:blog_categories,slug,'.$category->id],
            'description' => ['nullable','string','max:500'],
        ]));
        return back()->with('success', 'تم التحديث.');
    }

    public function destroy(BlogCategory $category)
    {
        $category->delete();
        return back()->with('success', 'تم الحذف.');
    }
}
