<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogPostController extends Controller
{
    public function index(Request $request)
    {
        $posts = BlogPost::with('category:id,name')
            ->when($request->q, fn ($q) => $q->where('title', 'like', "%{$request->q}%"))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.content.blog.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.content.blog.form', [
            'post' => new BlogPost(),
            'categories' => BlogCategory::orderBy('name')->get(['id','name']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('blog', 'public');
        }
        $data['author_id'] = auth('admin')->id();
        BlogPost::create($data);

        return redirect()->route('admin.blog.index')->with('success', 'تم إنشاء المقال.');
    }

    public function edit(BlogPost $blog)
    {
        return view('admin.content.blog.form', [
            'post' => $blog,
            'categories' => BlogCategory::orderBy('name')->get(['id','name']),
        ]);
    }

    public function update(Request $request, BlogPost $blog)
    {
        $data = $this->validated($request, $blog->id);
        if ($request->hasFile('image')) {
            if ($blog->image) Storage::disk('public')->delete($blog->image);
            $data['image'] = $request->file('image')->store('blog', 'public');
        }
        $blog->update($data);

        return redirect()->route('admin.blog.index')->with('success', 'تم تحديث المقال.');
    }

    public function destroy(BlogPost $blog)
    {
        if ($blog->image) Storage::disk('public')->delete($blog->image);
        $blog->delete();
        return back()->with('success', 'تم الحذف.');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'blog_category_id' => ['nullable','exists:blog_categories,id'],
            'title' => ['required','string','max:255'],
            'slug' => ['nullable','string','max:255','unique:blog_posts,slug,'.($id ?? 'NULL')],
            'excerpt' => ['nullable','string','max:500'],
            'content' => ['required','string'],
            'image' => ['nullable','image','max:4096'],
            'published_at' => ['nullable','date'],
        ]);
    }
}
