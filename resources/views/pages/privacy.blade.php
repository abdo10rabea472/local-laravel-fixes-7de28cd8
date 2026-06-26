@extends('layouts.front')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/praivacy-policy.css') }}">
<link rel="stylesheet" href="{{ asset('css/FAQS-PAGE.css') }}">
@endpush

@section('content')
<section class="hero-bg text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-16 sm:py-20 text-center">
        <span class="inline-flex items-center gap-2 bg-white/20 backdrop-blur px-5 py-2 rounded-full text-sm font-medium mb-5">
            <i class="fa-solid fa-shield-halved"></i> Your Privacy Matters
        </span>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight mb-4">{{ $page?->title ?: 'Privacy Policy' }}</h1>
        <p class="text-lg text-white/90 max-w-2xl mx-auto">We are committed to protecting your personal information with transparency and security.</p>
        <p class="text-sm text-white/70 mt-6">Last updated: {{ $page?->updated_at?->format('F d, Y') ?: 'April 15, 2026' }}</p>
    </div>
</section>

<main class="max-w-4xl mx-auto px-4 sm:px-6 py-12 space-y-8">
    @if($page?->content)
        <section class="policy-card bg-white rounded-3xl shadow-sm p-8 lg:p-10 border border-slate-100 prose max-w-none">
            {!! $page->content !!}
        </section>
    @else
        @foreach([
            ['title' => 'Introduction', 'body' => 'Welcome to UNI-LAB MARKET, your trusted online store for laboratory equipment and educational tools. This policy explains how we collect, use, store, and protect your personal information when you visit our website or make a purchase.'],
            ['title' => 'Information We Collect', 'body' => 'We collect your name, phone number, email, and shipping address when you place an order. We may also collect university or college details optionally. We do not store credit card details — payments are processed through secure third-party gateways.'],
            ['title' => 'How We Use Your Information', 'body' => 'Your data is used to process orders, arrange delivery, provide customer support, send order updates, and improve our services. We never sell your personal information to third parties.'],
            ['title' => 'Data Security', 'body' => 'We use industry-standard encryption and secure servers to protect your data. Access to personal information is restricted to authorized staff only.'],
            ['title' => 'Your Rights', 'body' => 'You may request access, correction, or deletion of your personal data at any time by contacting ' . site_setting('contact_email', 'ahmedkhamis@gmail.com') . '.'],
            ['title' => 'Contact Us', 'body' => 'For privacy-related questions: ' . site_setting('contact_email', 'ahmedkhamis@gmail.com') . ' | ' . site_setting('contact_phone', '01007970340') . ' | ' . site_setting('contact_address', 'El Minya, Egypt') . '.'],
        ] as $i => $section)
        <section class="policy-card bg-white rounded-3xl shadow-sm p-8 lg:p-10 border border-slate-100">
            <div class="flex items-center gap-3 mb-5">
                <span class="w-10 h-10 bg-teal-100 text-teal-700 rounded-xl flex items-center justify-center font-black text-sm">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                <h2 class="text-2xl font-bold text-slate-900">{{ $section['title'] }}</h2>
            </div>
            <p class="text-slate-700 leading-relaxed">{{ $section['body'] }}</p>
        </section>
        @endforeach
    @endif
</main>
@endsection
