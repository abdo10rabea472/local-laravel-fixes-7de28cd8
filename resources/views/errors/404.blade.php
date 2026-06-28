@extends('layouts.front')

@section('content')
<section class="min-h-[70vh] flex items-center bg-gradient-to-br from-slate-50 via-violet-50 to-indigo-50 py-20">
    <div class="max-w-3xl mx-auto px-4 text-center">
        <div class="relative inline-block">
            <h1 class="text-[10rem] md:text-[14rem] font-black bg-gradient-to-br from-violet-600 to-indigo-700 bg-clip-text text-transparent leading-none">404</h1>
            <div class="absolute inset-0 flex items-center justify-center -z-0 opacity-10">
                <i class="fas fa-flask text-[12rem] text-violet-600"></i>
            </div>
        </div>

        <h2 class="text-2xl md:text-3xl font-bold text-slate-800 mt-4 mb-3">
            عذرًا، الصفحة غير موجودة
        </h2>
        <p class="text-slate-600 mb-8 max-w-md mx-auto leading-relaxed">
            الرابط الذي تحاول الوصول إليه قد يكون منقولًا أو محذوفًا أو غير صحيح.
        </p>

        <div class="flex flex-wrap gap-3 justify-center">
            <a href="{{ url('/') }}" class="px-6 py-3 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-xl shadow-lg shadow-violet-500/30 transition">
                <i class="fas fa-home"></i> العودة للرئيسية
            </a>
            <a href="{{ route('blog.index') }}" class="px-6 py-3 bg-white hover:bg-slate-100 text-slate-700 font-bold rounded-xl border border-slate-200 transition">
                <i class="fas fa-newspaper"></i> تصفح المدونة
            </a>
            <a href="{{ route('contact') }}" class="px-6 py-3 bg-white hover:bg-slate-100 text-slate-700 font-bold rounded-xl border border-slate-200 transition">
                <i class="fas fa-envelope"></i> تواصل معنا
            </a>
        </div>
    </div>
</section>
@endsection
