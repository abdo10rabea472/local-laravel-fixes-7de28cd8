@extends('admin.layouts.app')
@section('title', $post->exists ? 'تعديل مقال' : 'مقال جديد')
@section('content')
<div class="p-6 max-w-5xl">
    <h1 class="text-2xl font-bold mb-6">{{ $post->exists ? 'تعديل مقال' : 'مقال جديد' }}</h1>

    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><ul class="list-disc pr-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="{{ $post->exists ? route('admin.blog.update', $post) : route('admin.blog.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf @if($post->exists) @method('PUT') @endif

        {{-- Main content card --}}
        <div class="bg-white p-6 rounded-xl shadow space-y-4">
            <div>
                <label class="block text-sm font-semibold mb-1">العنوان *</label>
                <input id="title" name="title" value="{{ old('title', $post->title) }}" required class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Slug (الرابط)</label>
                    <input id="slug" name="slug" value="{{ old('slug', $post->slug) }}" class="w-full px-3 py-2 border rounded-lg font-mono text-sm" dir="ltr">
                    <p class="text-xs text-slate-500 mt-1">اتركه فارغًا لتوليده تلقائيًا من العنوان.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">التصنيف (من تصنيفات المنتجات)</label>
                    <select name="blog_category_id" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">— بدون تصنيف —</option>
                        @php $byParent = $categories->groupBy('parent_id'); @endphp
                        @foreach($byParent->get(null, collect())->merge($byParent->get(0, collect())) as $root)
                            <option value="{{ $root->id }}" @selected(old('blog_category_id', $post->blog_category_id) == $root->id)>{{ $root->name }}</option>
                            @foreach($byParent->get($root->id, []) as $child)
                                <option value="{{ $child->id }}" @selected(old('blog_category_id', $post->blog_category_id) == $child->id)>— {{ $child->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">مقتطف قصير</label>
                <textarea name="excerpt" rows="2" maxlength="500" class="w-full px-3 py-2 border rounded-lg">{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">المحتوى *</label>
                <textarea id="content-editor" name="content" rows="20" class="w-full px-3 py-2 border rounded-lg">{{ old('content', $post->content) }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">الصورة الرئيسية</label>
                    <input type="file" name="image" accept="image/*" class="w-full">
                    @if($post->image)<img src="{{ asset('storage/'.$post->image) }}" class="mt-2 h-24 rounded shadow">@endif
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">تاريخ النشر</label>
                    <input type="datetime-local" name="published_at" value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}" class="w-full px-3 py-2 border rounded-lg">
                    <p class="text-xs text-slate-500 mt-1">اتركه فارغًا للنشر فورًا.</p>
                </div>
            </div>
        </div>

        {{-- SEO card --}}
        <div class="bg-white p-6 rounded-xl shadow space-y-4">
            <div class="flex items-center gap-2 border-b pb-3">
                <i class="fas fa-search text-violet-600"></i>
                <h3 class="font-bold text-lg">تحسين محركات البحث (SEO)</h3>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Meta Title</label>
                <input name="meta_title" value="{{ old('meta_title', $post->meta_title) }}" maxlength="60" class="w-full px-3 py-2 border rounded-lg" oninput="document.getElementById('mt-count').innerText=this.value.length">
                <p class="text-xs text-slate-500 mt-1">المثالي ≤ 60 حرف — <span id="mt-count">{{ strlen($post->meta_title ?? '') }}</span>/60</p>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Meta Description</label>
                <textarea name="meta_description" rows="3" maxlength="160" class="w-full px-3 py-2 border rounded-lg" oninput="document.getElementById('md-count').innerText=this.value.length">{{ old('meta_description', $post->meta_description) }}</textarea>
                <p class="text-xs text-slate-500 mt-1">المثالي ≤ 160 حرف — <span id="md-count">{{ strlen($post->meta_description ?? '') }}</span>/160</p>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Keywords (مفصولة بفواصل)</label>
                <input name="meta_keywords" value="{{ old('meta_keywords', $post->meta_keywords) }}" placeholder="laravel, php, seo" class="w-full px-3 py-2 border rounded-lg" dir="ltr">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">صورة المشاركة (OG Image)</label>
                    <input type="file" name="og_image" accept="image/*" class="w-full">
                    @if($post->og_image)<img src="{{ asset('storage/'.$post->og_image) }}" class="mt-2 h-20 rounded shadow">@endif
                    <p class="text-xs text-slate-500 mt-1">1200×630 يوصى به.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Canonical URL</label>
                    <div class="flex items-stretch" dir="ltr">
                        <span class="inline-flex items-center px-3 bg-slate-100 border border-l-0 border-slate-300 rounded-l-lg text-xs text-slate-600 font-mono">{{ rtrim(url('/'), '/') }}/</span>
                        <input name="canonical_url" type="text" value="{{ old('canonical_url', $post->canonical_url) }}" placeholder="blog/your-slug" class="flex-1 px-3 py-2 border rounded-r-lg font-mono text-sm" dir="ltr">
                    </div>
                    <p class="text-xs text-slate-500 mt-1">اكتب المسار فقط بعد رابط الموقع. اتركه فارغًا لاستخدام رابط المقال الافتراضي.</p>
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input type="hidden" name="no_index" value="0">
                <input type="checkbox" name="no_index" value="1" @checked(old('no_index', $post->no_index))>
                منع الفهرسة (noindex) — لا تظهر في نتائج البحث.
            </label>

            {{-- Live SERP preview --}}
            <div class="mt-4 p-4 bg-slate-50 rounded-lg border">
                <p class="text-xs text-slate-500 mb-2">معاينة نتيجة بحث Google:</p>
                <div class="bg-white p-3 rounded border max-w-xl" dir="ltr">
                    <p class="text-xs text-emerald-700 truncate">{{ url('/blog/'.($post->slug ?: 'your-slug')) }}</p>
                    <p class="text-blue-700 text-lg leading-tight truncate" id="serp-title">{{ $post->meta_title ?: ($post->title ?: 'عنوان المقال') }}</p>
                    <p class="text-sm text-slate-600 line-clamp-2" id="serp-desc">{{ $post->meta_description ?: ($post->excerpt ?: 'وصف المقال يظهر هنا...') }}</p>
                </div>
            </div>
        </div>

        <div class="flex gap-2">
            <button class="px-6 py-2 bg-primary-600 text-white rounded-lg font-semibold"><i class="fas fa-save"></i> حفظ</button>
            <a href="{{ route('admin.blog.index') }}" class="px-6 py-2 bg-slate-100 rounded-lg">إلغاء</a>
        </div>
    </form>
</div>

{{-- TinyMCE rich editor (free, no API key needed when self-hosted via jsdelivr) --}}
<script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#content-editor',
        height: 600,
        directionality: 'rtl',
        language: 'ar',
        language_url: 'https://cdn.jsdelivr.net/npm/tinymce-i18n@latest/langs7/ar.js',
        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount emoticons codesample',
        toolbar: 'undo redo | blocks fontsize | bold italic underline strikethrough forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media table codesample | removeformat code fullscreen preview',
        toolbar_mode: 'wrap',
        menubar: 'edit view insert format tools table help',
        image_advtab: true,
        branding: false,
        promotion: false,
        content_style: 'body { font-family: Inter, system-ui, sans-serif; font-size: 15px; line-height: 1.7; }',
    });

    // Live SERP preview
    document.querySelector('[name=meta_title]')?.addEventListener('input', e => {
        document.getElementById('serp-title').textContent = e.target.value || document.getElementById('title').value || 'عنوان المقال';
    });
    document.querySelector('[name=meta_description]')?.addEventListener('input', e => {
        document.getElementById('serp-desc').textContent = e.target.value || 'وصف المقال يظهر هنا...';
    });
</script>
@endsection
