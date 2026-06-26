@extends('layouts.front')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Return-Refunds.css') }}">
<link rel="stylesheet" href="{{ asset('css/FAQS-PAGE.css') }}">
@endpush

@section('content')
<section class="hero-bg text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-16 sm:py-20 text-center">
        <span class="inline-flex items-center gap-2 bg-white/20 backdrop-blur px-5 py-2 rounded-full text-sm font-medium mb-5">
            <i class="fa-solid fa-rotate-left"></i> Hassle-Free Returns
        </span>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight mb-4">{{ $page?->title ?: 'Returns & Refunds' }}</h1>
        <p class="text-lg text-white/90 max-w-2xl mx-auto">Our clear and fair return policy for laboratory equipment and educational tools.</p>
        <p class="text-sm text-white/70 mt-6"><i class="fa-solid fa-clock mr-1"></i> 30-Day Return Window</p>
    </div>
</section>

<main class="max-w-4xl mx-auto px-4 sm:px-6 py-12 space-y-8">
    @if($page?->content)
        <section class="policy-card bg-white rounded-3xl shadow-sm p-8 lg:p-10 border border-slate-100 prose max-w-none">
            {!! $page->content !!}
        </section>
    @else
        @foreach([
            ['title' => 'Overview', 'body' => 'If you are not completely satisfied, you may return most items within 30 days of delivery. All returns must be in original condition with accessories, manuals, and packaging included.'],
            ['title' => 'Eligibility', 'body' => 'Returns are accepted for defective or damaged items, wrong items received, or change of mind on standard products. Large equipment may incur a restocking fee.'],
            ['title' => 'Return Process', 'body' => 'Contact us at ' . site_setting('contact_email', 'ahmedkhamis@gmail.com') . ' with your order number and reason for return. We will provide return instructions and a reference number within 24 hours.'],
            ['title' => 'Refunds', 'body' => 'Refunds are processed within 5–10 business days after we receive and inspect the returned item. Refunds go to the original payment method.'],
            ['title' => 'Non-Returnable Items', 'body' => 'Opened consumables, custom-ordered equipment, and items marked as final sale cannot be returned unless defective.'],
            ['title' => 'Contact', 'body' => 'Email: ' . site_setting('contact_email', 'ahmedkhamis@gmail.com') . ' | Phone: ' . site_setting('contact_phone', '01007970340') . ' | ' . site_setting('contact_address', 'El Minya, Egypt') . '.'],
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
