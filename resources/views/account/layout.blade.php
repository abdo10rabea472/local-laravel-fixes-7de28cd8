@extends('layouts.front')
@section('content')
<div class="bg-slate-50 min-h-screen">
    {{-- Account hero --}}
    @php $u = auth()->user(); @endphp
    <div class="relative overflow-hidden bg-gradient-to-br from-sky-600 to-indigo-700 p-5 text-white text-white">
        <div class="relative max-w-6xl mx-auto px-4 py-10 flex items-center gap-5">
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/15 backdrop-blur border border-white/25 grid place-items-center text-3xl font-black shrink-0">
                {{ mb_substr($u->name, 0, 1) }}
            </div>
            <div class="min-w-0">
                <nav class="text-[11px] font-bold text-blue-100/80 mb-1 flex items-center gap-2">
                    <a href="{{ route('home') }}" class="hover:text-white">{{ __('app.acc_home') }}</a>
                    <i class="fa-solid fa-chevron-right text-[8px]"></i>
                    <span class="text-white">{{ __('app.acc_my_account') }}</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight truncate">{{ $u->name }}</h1>
                <p class="text-blue-100 text-sm truncate">{{ $u->email }}</p>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 py-8 lg:grid lg:grid-cols-[16rem_1fr] lg:gap-8 lg:items-start" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

        @include('account.partials.sidebar')
        <div class="min-w-0">
            @yield('account_content')
        </div>
    </div>
</div>
@endsection
