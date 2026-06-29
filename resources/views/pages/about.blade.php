@extends('layouts.front')

@php
    // Safelist tailwind color classes used dynamically
    // bg-violet-50 bg-indigo-50 bg-emerald-50 bg-amber-50 bg-rose-50 bg-sky-50 bg-blue-50 bg-fuchsia-50 bg-teal-50 bg-orange-50
    // text-violet-700 text-indigo-700 text-emerald-700 text-amber-700 text-rose-700 text-sky-700 text-blue-700 text-fuchsia-700 text-teal-700 text-orange-700
    // bg-violet-100 bg-indigo-100 bg-emerald-100 bg-amber-100 bg-rose-100 bg-sky-100 bg-blue-100 bg-fuchsia-100 bg-teal-100 bg-orange-100
    // text-violet-600 text-indigo-600 text-emerald-600 text-amber-600 text-rose-600 text-sky-600 text-blue-600 text-fuchsia-600 text-teal-600 text-orange-600
@endphp

@section('content')
<section class="bg-gradient-to-br from-violet-600 via-violet-700 to-indigo-800 text-white py-20">
    <div class="max-w-6xl mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ $about['hero']['title'] }}</h1>
        <p class="text-lg text-violet-100 max-w-2xl mx-auto">{{ $about['hero']['subtitle'] }}</p>
    </div>
</section>

<section class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-4 grid md:grid-cols-2 gap-12 items-center">
        <div>
            <h2 class="text-3xl font-bold text-slate-800 mb-4">{{ $about['story']['title'] }}</h2>
            <p class="text-slate-600 leading-relaxed mb-4">{{ $about['story']['p1'] }}</p>
            <p class="text-slate-600 leading-relaxed">{{ $about['story']['p2'] }}</p>
        </div>
        <div class="grid grid-cols-2 gap-4">
            @foreach($about['stats'] as $stat)
                @php $c = $stat['color'] ?? 'violet'; @endphp
                <div class="bg-{{ $c }}-50 p-6 rounded-2xl text-center">
                    <div class="text-3xl font-bold text-{{ $c }}-700">{{ $stat['value'] }}</div>
                    <div class="text-sm text-slate-600 mt-1">{{ $stat['label'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-16 bg-slate-50">
    <div class="max-w-6xl mx-auto px-4 grid md:grid-cols-3 gap-8">
        @foreach($about['cards'] as $card)
            @php $c = $card['color'] ?? 'violet'; @endphp
            <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition">
                <div class="w-14 h-14 bg-{{ $c }}-100 text-{{ $c }}-600 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas {{ $card['icon'] }} text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">{{ $card['title'] }}</h3>
                <p class="text-slate-600">{{ $card['desc'] }}</p>
            </div>
        @endforeach
    </div>
</section>

@if(!empty($about['team']))
<section class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-slate-800 text-center mb-12">{{ $about['team_title'] }}</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($about['team'] as $member)
            <div class="text-center">
                <div class="w-32 h-32 mx-auto bg-gradient-to-br from-violet-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-4xl font-bold mb-3 shadow-lg">
                    {{ mb_substr($member['name'] ?: '?', 0, 1) }}
                </div>
                <h3 class="font-bold text-slate-800">{{ $member['name'] }}</h3>
                <p class="text-sm text-slate-500">{{ $member['role'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
