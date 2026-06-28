@extends('admin.layouts.app')
@section('title', 'Blog Posts')

@section('content')
<x-admin.page title="Blog Posts" subtitle="Manage all blog articles and SEO settings.">
    <x-admin.card title="All Posts" icon="fa-newspaper" padding="p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-dark-800 text-gray-500 dark:text-gray-400 text-xs">
                    <tr>
                        <th class="p-3 text-left">Title</th>
                        <th class="p-3">Category</th>
                        <th class="p-3">Views</th>
                        <th class="p-3">Publish</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($posts as $p)
                    <tr class="border-t border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-dark-800/50">
                        <td class="p-3 font-bold text-gray-900 dark:text-white">{{ $p->title }}</td>
                        <td class="p-3 text-center text-xs text-gray-600 dark:text-gray-400">{{ $p->category?->name ?? '—' }}</td>
                        <td class="p-3 text-center text-xs">{{ $p->views }}</td>
                        <td class="p-3 text-center text-xs">
                            @if($p->published_at)
                                <span class="px-2 py-1 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 rounded-full font-bold">{{ $p->published_at->format('Y-m-d') }}</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 dark:bg-dark-800 text-gray-600 dark:text-gray-400 rounded-full font-bold">Draft</span>
                            @endif
                        </td>
                        <td class="p-3 text-center whitespace-nowrap">
                            @if($p->published_at)
                                <a href="{{ route('blog.show', $p->slug) }}" target="_blank" class="text-emerald-600 hover:underline text-xs font-bold"><i class="fa-solid fa-eye"></i> View</a>
                            @endif
                            <a href="{{ route('admin.blog.edit', $p) }}" class="text-primary-600 hover:underline text-xs font-bold ml-2">Edit</a>
                            <form action="{{ route('admin.blog.destroy', $p) }}" method="POST" class="inline" onsubmit="return confirm('Delete post?')">
                                @csrf @method('DELETE')
                                <button class="text-rose-600 hover:underline text-xs font-bold ml-2">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-12 text-center text-gray-400">
                        <i class="fas fa-newspaper text-3xl mb-3 block"></i>
                        No posts yet.
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($posts->hasPages())
        <div class="p-4 border-t border-gray-100 dark:border-gray-800">{{ $posts->links() }}</div>
        @endif
    </x-admin.card>

    <x-slot:side>
        <x-admin.card title="Quick Actions" icon="fa-bolt">
            <a href="{{ route('admin.blog.create') }}" class="w-full h-12 inline-flex items-center justify-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl shadow-lg shadow-primary-500/20 transition-colors">
                <i class="fa-solid fa-plus"></i> New Post
            </a>
        </x-admin.card>

        <x-admin.card title="Search" icon="fa-search">
            <form method="GET" class="space-y-3">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Title or content..."
                       class="w-full h-11 px-4 bg-gray-50 dark:bg-dark-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:border-primary-500 focus:outline-none">
                <button class="w-full h-11 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl text-sm font-bold">Search</button>
            </form>
        </x-admin.card>
    </x-slot:side>
</x-admin.page>
@endsection
