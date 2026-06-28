@extends('layouts.front')

@section('title', $seo['seo_title'] ?? $post->title)
@section('meta_description', $seo['seo_description'] ?? $post->excerpt)
@section('meta_keywords', $seo['seo_keywords'] ?? '')

@section('content')
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

    {{-- ============ COVER IMAGE ============ --}}
    @if($post->image_url)
    <section class="max-w-5xl mx-auto px-6 lg:px-8 mb-16">
        <div class="relative aspect-[16/9] rounded-[2rem] overflow-hidden shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] ring-1 ring-slate-100">
            <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="absolute inset-0 w-full h-full object-cover">
        </div>
    </section>
    @endif

    {{-- ============ ARTICLE BODY ============ --}}
    <section class="max-w-3xl mx-auto px-6 lg:px-8">
        <article class="prose prose-lg prose-slate max-w-none prose-headings:font-bold prose-headings:text-slate-900 prose-a:text-indigo-600 prose-img:rounded-2xl">
            {!! $post->content !!}
        </article>
    </section>

    {{-- ============ RELATED ============ --}}
    @if($related && $related->count())
    <section class="max-w-7xl mx-auto px-6 lg:px-8 mt-24">
        <h3 class="text-2xl font-bold text-slate-900 mb-10">Related Articles</h3>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($related as $rel)
            <article class="group flex flex-col bg-white rounded-[1.5rem] overflow-hidden shadow-[0_4px_20px_-10px_rgba(0,0,0,0.05)] ring-1 ring-slate-100 transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_15px_30px_-10px_rgba(0,0,0,0.1)]">
                <a href="{{ route('blog.show', $rel->slug) }}" class="block relative aspect-[16/10] overflow-hidden">
                    <img src="{{ $rel->image_url }}" alt="{{ $rel->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    @if($rel->category)
                        <div class="absolute top-4 start-4 bg-white/90 backdrop-blur-md px-3 py-1.5 rounded-lg text-[11px] font-bold text-slate-800 shadow-sm">
                            {{ $rel->category->name }}
                        </div>
                    @endif
                </a>
                <div class="p-6 flex flex-col flex-1">
                    <h4 class="text-lg font-bold text-slate-900 leading-snug mb-3 group-hover:text-indigo-600 transition-colors">
                        <a href="{{ route('blog.show', $rel->slug) }}">{{ $rel->title }}</a>
                    </h4>
                    <p class="text-slate-600 leading-relaxed line-clamp-2 mb-4 text-sm flex-1">{{ $rel->excerpt }}</p>
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100 text-xs font-medium text-slate-500">
                        <span class="flex items-center gap-1.5"><i class="far fa-calendar"></i> {{ $rel->published_at?->format('M d, Y') }}</span>
                        <span class="flex items-center gap-1.5 group-hover:text-indigo-600 transition-colors">Read <i class="fas fa-arrow-right text-[10px]"></i></span>
                    </div>
                </div>
            </article>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ============ COMMENTS ============ --}}
    <section id="comments" class="max-w-3xl mx-auto px-6 lg:px-8 mt-24">
        <h3 class="text-2xl font-bold text-slate-900 mb-8">Comments ({{ $post->approvedComments->count() }})</h3>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="space-y-6 mb-12">
            @forelse($post->approvedComments as $comment)
                <div class="p-6 bg-white rounded-2xl ring-1 ring-slate-100 shadow-sm">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-white flex items-center justify-center font-bold">
                            {{ mb_substr($comment->name, 0, 1) }}
                        </div>
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
                    <input type="text" name="name" placeholder="Your Name" value="{{ old('name') }}" required
                           class="w-full p-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none text-sm">
                    <input type="email" name="email" placeholder="Your Email" value="{{ old('email') }}" required
                           class="w-full p-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none text-sm">
                </div>
            @endguest

            <textarea name="body" rows="4" placeholder="Write your comment..." required
                      class="w-full p-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none text-sm">{{ old('body') }}</textarea>

            @error('body')<p class="text-sm text-rose-600">{{ $message }}</p>@enderror

            <button type="submit" class="px-6 py-3 bg-slate-900 hover:bg-indigo-600 text-white rounded-xl text-sm font-medium transition-colors shadow-sm">
                Post Comment
            </button>
        </form>
    </section>

</div>
@endsection
