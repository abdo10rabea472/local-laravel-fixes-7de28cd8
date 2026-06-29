@extends('layouts.front')

@section('title', $seo['seo_title'] ?? $post->title)
@section('meta_description', $seo['seo_description'] ?? $post->excerpt)
@section('meta_keywords', $seo['seo_keywords'] ?? '')

@push('schemas')
@php
    $_articleSchema = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $post->title,
        'description' => $post->excerpt,
        'image' => $post->image ? asset('storage/'.$post->image) : null,
        'datePublished' => optional($post->published_at)->toIso8601String(),
        'dateModified' => optional($post->updated_at)->toIso8601String(),
        'author' => ['@type' => 'Person', 'name' => $post->author_name ?? site_setting('site_name', 'UNI-LAB MARKET')],
        'publisher' => [
            '@type' => 'Organization',
            'name' => site_setting('site_name', 'UNI-LAB MARKET'),
            'logo' => ['@type' => 'ImageObject', 'url' => site_setting_url('site_logo', asset('imges/photo_٢٠٢٦-٠٢-٢٥_٠٨-٤٧-٣٧-removebg-preview.png'))],
        ],
        'mainEntityOfPage' => url()->current(),
    ]);
    $_crumbs = [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'Blog', 'url' => route('blog.index')],
    ];
    if ($post->category) {
        $_crumbs[] = ['name' => $post->category->name, 'url' => route('blog.index', ['category' => $post->category->slug])];
    }
    $_crumbs[] = ['name' => $post->title, 'url' => url()->current()];
    $_breadSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => array_map(fn($i, $c) => [
            '@type' => 'ListItem', 'position' => $i + 1, 'name' => $c['name'], 'item' => $c['url'],
        ], array_keys($_crumbs), $_crumbs),
    ];
@endphp
<script type="application/ld+json">{!! json_encode($_articleSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
<script type="application/ld+json">{!! json_encode($_breadSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush


@section('content')
@php
    $_breadItems = [
        ['name' => 'الرئيسية', 'url' => url('/')],
        ['name' => 'المدونة', 'url' => route('blog.index')],
    ];
    if ($post->category) { $_breadItems[] = ['name' => $post->category->name, 'url' => route('blog.index', ['category' => $post->category->slug])]; }
    $_breadItems[] = ['name' => $post->title];
@endphp
<div class="max-w-4xl mx-auto px-6 lg:px-8">
    <x-breadcrumbs :items="$_breadItems" />
</div>

<div class="min-h-screen bg-[#F8FAFC] text-slate-800 font-sans selection:bg-indigo-500 selection:text-white pb-24">

    {{-- ============ HERO ============ --}}
    <section class="relative pt-16 pb-10 overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-full -z-10 pointer-events-none">
            <div class="absolute -top-40 right-20 w-96 h-96 bg-indigo-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70"></div>
            <div class="absolute top-0 left-20 w-96 h-96 bg-purple-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70"></div>
        </div>

        <div class="max-w-4xl mx-auto px-6 lg:px-8 text-center">
            <nav class="text-sm text-slate-500 mb-6">
                <a href="{{ route('home') }}" class="hover:text-indigo-600">Home</a>
                <span class="mx-2">/</span>
                <a href="{{ route('blog.index') }}" class="hover:text-indigo-600">Blog</a>
                @if($post->category)
                    <span class="mx-2">/</span>
                    <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}" class="hover:text-indigo-600">{{ $post->category->name }}</a>
                @endif
            </nav>

            @if($post->category)
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 text-xs font-semibold tracking-wide uppercase ring-1 ring-inset ring-indigo-500/20 mb-6">
                    {{ $post->category->name }}
                </span>
            @endif

            <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight mb-6 leading-tight">
                {{ $post->title }}
            </h1>

            @if($post->excerpt)
                <p class="text-lg text-slate-600 leading-relaxed mb-8 max-w-2xl mx-auto">{{ $post->excerpt }}</p>
            @endif

            <div class="flex items-center justify-center gap-5 text-sm text-slate-500 font-medium">
                <span class="flex items-center gap-2"><i class="far fa-calendar"></i> {{ $post->published_at?->format('M d, Y') }}</span>
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span class="flex items-center gap-2"><i class="far fa-eye"></i> {{ $post->views }} views</span>
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span class="flex items-center gap-2"><i class="far fa-comment"></i> {{ $post->approvedComments->count() }} comments</span>
            </div>
        </div>
    </section>

    {{-- ============ MAIN GRID ============ --}}
    <div class="max-w-7xl mx-auto px-6 lg:px-8 grid lg:grid-cols-3 gap-10">

        {{-- ============ MAIN ============ --}}
        <main class="lg:col-span-2 space-y-12">

            {{-- COVER --}}
            @if($post->image_url)
            <div class="relative aspect-[16/9] rounded-[2rem] overflow-hidden shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] ring-1 ring-slate-100">
                <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="absolute inset-0 w-full h-full object-cover">
            </div>
            @endif

            {{-- BODY --}}
            <article class="prose prose-lg prose-slate max-w-none prose-headings:font-bold prose-headings:text-slate-900 prose-a:text-indigo-600 prose-img:rounded-2xl">
                {!! app(\App\Services\InternalLinker::class)->linkProductMentions($post->content) !!}
            </article>

            {{-- RELATED --}}
            @if($related && $related->count())
            <section>
                <h3 class="text-2xl font-bold text-slate-900 mb-8">Related Articles</h3>
                <div class="grid sm:grid-cols-2 gap-6">
                    @foreach($related as $rel)
                    <article class="group flex flex-col bg-white rounded-2xl overflow-hidden shadow-[0_4px_20px_-10px_rgba(0,0,0,0.05)] ring-1 ring-slate-100 transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_15px_30px_-10px_rgba(0,0,0,0.1)]">
                        <a href="{{ route('blog.show', $rel->slug) }}" class="block relative aspect-[16/10] overflow-hidden">
                            <img src="{{ $rel->image_url }}" alt="{{ $rel->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                            @if($rel->category)
                                <div class="absolute top-3 start-3 bg-white/90 backdrop-blur-md px-3 py-1 rounded-lg text-[11px] font-bold text-slate-800 shadow-sm">{{ $rel->category->name }}</div>
                            @endif
                        </a>
                        <div class="p-5 flex flex-col flex-1">
                            <h4 class="text-base font-bold text-slate-900 leading-snug mb-2 group-hover:text-indigo-600 transition-colors line-clamp-2">
                                <a href="{{ route('blog.show', $rel->slug) }}">{{ $rel->title }}</a>
                            </h4>
                            <p class="text-slate-600 leading-relaxed line-clamp-2 text-sm flex-1">{{ $rel->excerpt }}</p>
                            <div class="flex items-center justify-between pt-3 mt-3 border-t border-slate-100 text-xs font-medium text-slate-500">
                                <span class="flex items-center gap-1.5"><i class="far fa-calendar"></i> {{ $rel->published_at?->format('M d, Y') }}</span>
                                <span class="flex items-center gap-1.5 group-hover:text-indigo-600 transition-colors">Read <i class="fas fa-arrow-right text-[10px]"></i></span>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>
            </section>
            @endif

            {{-- COMMENTS --}}
            <section id="comments">
                <h3 class="text-2xl font-bold text-slate-900 mb-8">Comments ({{ $post->approvedComments->count() }})</h3>

                @if(session('success'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200 text-sm font-medium">{{ session('success') }}</div>
                @endif

                <div class="space-y-6 mb-10">
                    @forelse($post->approvedComments as $comment)
                        <div class="p-6 bg-white rounded-2xl ring-1 ring-slate-100 shadow-sm">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-white flex items-center justify-center font-bold">{{ mb_substr($comment->name, 0, 1) }}</div>
                                <div>
                                    <div class="font-bold text-slate-900 text-sm">{{ $comment->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $comment->created_at?->diffForHumans() }}</div>
                                </div>
                            </div>
                            <p class="text-slate-700 leading-relaxed text-sm whitespace-pre-line">{{ $comment->body }}</p>
                        </div>
                    @empty
                        <p class="text-slate-500 text-center py-8">Be the first to comment.</p>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('blog.comments.store', $post->slug) }}" class="p-6 bg-white rounded-2xl ring-1 ring-slate-100 shadow-sm space-y-4">
                    @csrf
                    <h4 class="text-lg font-bold text-slate-900">Leave a Comment</h4>
                    @guest('web')
                        <div class="grid md:grid-cols-2 gap-4">
                            <input type="text" name="name" placeholder="Your Name" value="{{ old('name') }}" required class="w-full p-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none text-sm">
                            <input type="email" name="email" placeholder="Your Email" value="{{ old('email') }}" required class="w-full p-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none text-sm">
                        </div>
                    @endguest
                    <textarea name="body" rows="4" placeholder="Write your comment..." required class="w-full p-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none text-sm">{{ old('body') }}</textarea>
                    @error('body')<p class="text-sm text-rose-600">{{ $message }}</p>@enderror
                    <button type="submit" class="px-6 py-3 bg-slate-900 hover:bg-indigo-600 text-white rounded-xl text-sm font-medium transition-colors shadow-sm">Post Comment</button>
                </form>
            </section>
        </main>

        {{-- ============ SIDEBAR ============ --}}
        <aside class="lg:col-span-1 space-y-6 lg:sticky lg:top-24 self-start">

            {{-- Search --}}
            <div class="p-5 bg-white rounded-2xl ring-1 ring-slate-100 shadow-sm">
                <h4 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2"><i class="fas fa-search text-indigo-600"></i> Search</h4>
                <form method="GET" action="{{ route('blog.index') }}" class="relative">
                    <input type="search" name="q" placeholder="Search articles..." class="w-full h-11 ps-4 pe-10 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                    <button class="absolute inset-y-0 end-0 px-3 text-slate-400 hover:text-indigo-600"><i class="fas fa-arrow-right"></i></button>
                </form>
            </div>

            {{-- Featured --}}
            @if(!empty($featured))
            <div class="p-5 bg-white rounded-2xl ring-1 ring-slate-100 shadow-sm">
                <h4 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2"><i class="fas fa-star text-amber-500"></i> Featured Article</h4>
                <a href="{{ route('blog.show', $featured->slug) }}" class="group block">
                    <div class="aspect-[16/10] rounded-xl overflow-hidden mb-3 bg-slate-100">
                        <img src="{{ $featured->image_url }}" alt="{{ $featured->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    </div>
                    @if($featured->category)
                        <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">{{ $featured->category->name }}</span>
                    @endif
                    <h5 class="font-bold text-slate-900 leading-snug mt-1 mb-2 group-hover:text-indigo-600 transition-colors line-clamp-2">{{ $featured->title }}</h5>
                    <p class="text-xs text-slate-500 line-clamp-2">{{ $featured->excerpt }}</p>
                </a>
            </div>
            @endif

            {{-- Popular --}}
            @if(!empty($popular) && $popular->count())
            <div class="p-5 bg-white rounded-2xl ring-1 ring-slate-100 shadow-sm">
                <h4 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2"><i class="fas fa-fire text-rose-500"></i> Most Viewed</h4>
                <ul class="space-y-4">
                    @foreach($popular as $i => $p)
                    <li>
                        <a href="{{ route('blog.show', $p->slug) }}" class="group flex gap-3 items-start">
                            <span class="shrink-0 w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-500 text-white text-xs font-bold flex items-center justify-center">{{ $i + 1 }}</span>
                            <div class="flex-1 min-w-0">
                                <h6 class="text-sm font-semibold text-slate-800 leading-snug line-clamp-2 group-hover:text-indigo-600 transition-colors">{{ $p->title }}</h6>
                                <div class="flex items-center gap-3 mt-1 text-[11px] text-slate-400 font-medium">
                                    <span><i class="far fa-calendar"></i> {{ $p->published_at?->format('M d, Y') }}</span>
                                    <span><i class="far fa-eye"></i> {{ $p->views }}</span>
                                </div>
                            </div>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Categories --}}
            @if(!empty($categories) && $categories->count())
            <div class="p-5 bg-white rounded-2xl ring-1 ring-slate-100 shadow-sm">
                <h4 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2"><i class="fas fa-folder text-indigo-600"></i> Categories</h4>
                <div class="space-y-1">
                    <a href="{{ route('blog.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50"><span>All</span></a>
                    @foreach($categories as $cat)
                    <a href="{{ route('blog.index', ['category' => $cat->slug]) }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $post->category && $post->category->slug===$cat->slug ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50' }}">
                        <span>{{ $cat->name }}</span>
                        <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Tags --}}
            @if(!empty($tags) && $tags->count())
            <div class="p-5 bg-white rounded-2xl ring-1 ring-slate-100 shadow-sm">
                <h4 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2"><i class="fas fa-tags text-purple-600"></i> Tags</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($tags as $tag)
                    <a href="{{ route('blog.index', ['tag' => $tag]) }}" class="px-3 py-1.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">#{{ $tag }}</a>
                    @endforeach
                </div>
            </div>
            @endif

        </aside>
    </div>

</div>
@endsection
