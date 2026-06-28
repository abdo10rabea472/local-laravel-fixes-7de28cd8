<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::published()->with('category:id,name,slug');

        if ($request->filled('category')) {
            $cat = BlogCategory::where('slug', $request->category)->first();
            if ($cat) $query->where('blog_category_id', $cat->id);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($w) => $w->where('title', 'like', "%$q%")->orWhere('excerpt', 'like', "%$q%"));
        }

        $posts = $query->latest('published_at')->paginate(9)->withQueryString();
        $categories = BlogCategory::orderBy('name')->get();

        return view('pages.blog.index', compact('posts', 'categories'));
    }

    public function show(string $slug)
    {
        $post = BlogPost::published()->with('category:id,name,slug')->where('slug', $slug)->firstOrFail();
        $post->increment('views');

        $related = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->where('blog_category_id', $post->blog_category_id)
            ->limit(3)->get();

        return view('pages.blog.show', compact('post', 'related'));
    }
}
