@extends('layouts.front')

@section('content')
<main class="max-w-3xl mx-auto px-4 py-12">
    <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-emerald-50 text-emerald-600 rounded-3xl mb-6 border border-emerald-100">
            <i class="fa-solid fa-check text-3xl"></i>
        </div>
        <p class="text-sm font-bold uppercase tracking-widest text-emerald-600 mb-2">Order Successfully Placed</p>
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 mb-3">{{ $page?->title ?: 'Your tools are on the way!' }}</h1>
        <p class="text-slate-500">{{ $page?->content ?: 'Thank you for shopping with UNI-LAB MARKET.' }}</p>
        <p class="text-slate-600 mt-2">Receipt sent to <strong id="success-email">your email</strong></p>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden">
        <div class="p-6 sm:p-8 bg-slate-900 text-white flex flex-col sm:flex-row justify-between gap-4">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Invoice</span>
                <h2 class="text-xl font-bold font-mono" id="success-invoice">#HZ-000000</h2>
            </div>
            <div class="bg-emerald-500/20 border border-emerald-500/30 px-4 py-2 rounded-xl self-start">
                <span class="text-emerald-400 text-xs font-extrabold uppercase">● Success</span>
            </div>
        </div>

        <div class="p-6 sm:p-8 grid sm:grid-cols-2 gap-6 bg-slate-50/60 border-b border-slate-200">
            <div class="bg-white p-5 rounded-2xl border border-slate-200">
                <h3 class="text-xs font-bold uppercase text-slate-400 mb-3"><i class="fa-solid fa-location-dot"></i> Shipping</h3>
                <p class="font-bold text-slate-900" id="success-name">—</p>
                <p class="text-sm text-slate-600" id="success-address">—</p>
                <p class="text-sm text-slate-600" id="success-city">—</p>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200">
                <h3 class="text-xs font-bold uppercase text-slate-400 mb-3"><i class="fa-solid fa-wallet"></i> Payment</h3>
                <p class="text-sm"><span class="text-slate-500">Method:</span> <span id="success-payment" class="font-semibold">—</span></p>
                <p class="text-sm mt-2"><span class="text-slate-500">Total:</span> <span id="success-total" class="font-black text-violet-600">—</span></p>
            </div>
        </div>

        <div class="p-6 sm:p-8" id="success-items"></div>

        <div class="p-6 border-t border-slate-100 flex flex-wrap gap-3 justify-center">
            <a href="{{ route('products.index') }}" class="px-6 py-3 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-2xl">Continue Shopping</a>
            <a href="{{ route('home') }}" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-2xl">Back to Home</a>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const order = JSON.parse(sessionStorage.getItem('lastOrder') || 'null');
    if (!order) return;

    const labels = { vodafone: 'Vodafone Cash', card: 'Credit Card', fawry: 'Fawry', cod: 'Cash on Delivery' };
    document.getElementById('success-email').textContent = order.email;
    document.getElementById('success-invoice').textContent = '#' + order.invoice;
    document.getElementById('success-name').textContent = order.firstName + ' ' + order.lastName;
    document.getElementById('success-address').textContent = order.address;
    document.getElementById('success-city').textContent = order.city + ', Egypt';
    document.getElementById('success-payment').textContent = labels[order.payment] || order.payment;
    document.getElementById('success-total').textContent = order.total.toLocaleString() + ' EGP';

    document.getElementById('success-items').innerHTML = (order.items || []).map(i => `
        <div class="flex gap-4 py-3 border-b border-slate-100 last:border-0">
            <img src="${i.image || ''}" class="h-14 w-14 object-contain bg-slate-50 rounded-lg" alt="">
            <div class="flex-1"><p class="font-semibold text-sm">${i.name}</p><p class="text-xs text-slate-500">Qty: ${i.quantity || 1}</p></div>
            <p class="font-bold text-sm">${(i.price * (i.quantity || 1)).toLocaleString()} EGP</p>
        </div>
    `).join('');
});
</script>
@endpush
