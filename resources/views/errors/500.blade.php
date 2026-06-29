@extends('layouts.front')

@section('content')
<section class="min-h-[70vh] flex items-center bg-gradient-to-br from-slate-50 via-rose-50 to-orange-50 py-20">
    <div class="max-w-2xl mx-auto px-4 text-center">
        <div class="relative inline-block">
            <h1 class="text-[10rem] md:text-[14rem] font-black bg-gradient-to-br from-rose-600 to-orange-600 bg-clip-text text-transparent leading-none">500</h1>
            <div class="absolute inset-0 flex items-center justify-center -z-0 opacity-10">
                <i class="fas fa-triangle-exclamation text-[12rem] text-rose-600"></i>
            </div>
        </div>

        <h2 class="text-2xl md:text-3xl font-bold text-slate-800 mt-4 mb-3">حدث خطأ غير متوقع</h2>
        <p class="text-slate-600 mb-8 max-w-md mx-auto leading-relaxed">
            نواجه مشكلة مؤقتة في الخادم. يرجى المحاولة بعد لحظات. إذا استمرت المشكلة، تواصل معنا.
        </p>

        <div class="flex flex-wrap gap-3 justify-center">
            <a href="{{ url('/') }}" class="px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-lg shadow-rose-500/30 transition">
                <i class="fas fa-home"></i> العودة للرئيسية
            </a>
            <button onclick="location.reload()" class="px-6 py-3 bg-white hover:bg-slate-100 text-slate-700 font-bold rounded-xl border border-slate-200 transition">
                <i class="fas fa-rotate"></i> إعادة المحاولة
            </button>
            <a href="{{ route('contact') }}" class="px-6 py-3 bg-white hover:bg-slate-100 text-slate-700 font-bold rounded-xl border border-slate-200 transition">
                <i class="fas fa-envelope"></i> تواصل معنا
            </a>
        </div>
    </div>
</section>
@endsection
