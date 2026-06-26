@extends('layouts.front')

@section('title', 'Checkout')

@section('content')
<main class="bg-slate-50 min-h-screen py-8 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('products.index') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900">Checkout</h1>
        </div>

        <div id="checkout-empty" class="hidden text-center py-20 bg-white rounded-3xl border border-slate-200">
            <i class="fa-solid fa-cart-shopping text-5xl text-slate-300 mb-4"></i>
            <h2 class="text-xl font-bold text-slate-800">Your cart is empty</h2>
            <p class="text-slate-500 mt-2 mb-6">Add some products before checking out.</p>
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-2xl transition-colors">
                Browse Products <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div id="checkout-content" class="grid gap-8 lg:grid-cols-5">
            {{-- Form --}}
            <div class="lg:col-span-3 space-y-6">
                <form id="checkout-form" class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-truck text-violet-600"></i> Shipping Information
                    </h2>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500">First Name</label>
                            <input type="text" name="first_name" required class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:border-violet-300 focus:bg-white transition-colors">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500">Last Name</label>
                            <input type="text" name="last_name" required class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:border-violet-300 focus:bg-white transition-colors">
                        </div>
                        <div class="space-y-1.5 sm:col-span-2">
                            <label class="text-xs font-bold text-slate-500">Email</label>
                            <input type="email" name="email" required class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:border-violet-300 focus:bg-white transition-colors">
                        </div>
                        <div class="space-y-1.5 sm:col-span-2">
                            <label class="text-xs font-bold text-slate-500">Address</label>
                            <input type="text" name="address" required class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:border-violet-300 focus:bg-white transition-colors">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500">Phone</label>
                            <input type="tel" name="phone" required class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:border-violet-300 focus:bg-white transition-colors">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500">Country</label>
                            <select id="shipping-country" name="country" required class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:border-violet-300 focus:bg-white transition-colors">
                                <option value="">Select country</option>
                            </select>
                        </div>
                        <div class="space-y-1.5 sm:col-span-2">
                            <label class="text-xs font-bold text-slate-500">State / City</label>
                            <select id="shipping-state" name="state" required class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:border-violet-300 focus:bg-white transition-colors">
                                <option value="">Select state</option>
                            </select>
                            <p id="unsupported-country" class="hidden text-xs text-rose-600 font-bold mt-1">
                                <i class="fa-solid fa-circle-info ml-1"></i> We do not support your country yet. It will be available soon.
                            </p>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-6">
                        <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2 mb-4">
                            <i class="fa-solid fa-credit-card text-violet-600"></i> Payment Method
                        </h2>

                        <div class="space-y-3">
                            <label class="flex items-start gap-3 p-4 rounded-2xl border border-slate-200 hover:border-violet-300 hover:bg-violet-50/30 cursor-pointer transition-colors has-[:checked]:border-violet-600 has-[:checked]:bg-violet-50/40">
                                <input type="radio" name="payment_method" value="cod" checked class="mt-1 text-violet-600 focus:ring-violet-500">
                                <div>
                                    <p class="font-bold text-slate-900">Cash on Delivery</p>
                                    <p class="text-xs text-slate-500">Pay when your order arrives</p>
                                </div>
                            </label>
                            <label class="flex items-start gap-3 p-4 rounded-2xl border border-slate-200 hover:border-violet-300 hover:bg-violet-50/30 cursor-pointer transition-colors has-[:checked]:border-violet-600 has-[:checked]:bg-violet-50/40">
                                <input type="radio" name="payment_method" value="vodafone" class="mt-1 text-violet-600 focus:ring-violet-500">
                                <div>
                                    <p class="font-bold text-slate-900">Vodafone Cash</p>
                                    <p class="text-xs text-slate-500">Pay using your Vodafone Cash wallet</p>
                                </div>
                            </label>
                            <label class="flex items-start gap-3 p-4 rounded-2xl border border-slate-200 hover:border-violet-300 hover:bg-violet-50/30 cursor-pointer transition-colors has-[:checked]:border-violet-600 has-[:checked]:bg-violet-50/40">
                                <input type="radio" name="payment_method" value="card" class="mt-1 text-violet-600 focus:ring-violet-500">
                                <div>
                                    <p class="font-bold text-slate-900">Credit / Debit Card</p>
                                    <p class="text-xs text-slate-500">Visa, Mastercard, or Meeza</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Summary --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm lg:sticky lg:top-28">
                    <h2 class="text-lg font-bold text-slate-900 mb-5">Order Summary</h2>
                    <div id="checkout-items" class="space-y-4 mb-5 max-h-80 overflow-y-auto pr-1"></div>

                    <div class="border-t border-dashed border-slate-200 pt-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Subtotal</span>
                            <span id="subtotal-display" class="font-bold text-slate-900">0 EGP</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Shipping</span>
                            <span id="shipping-display" class="font-bold text-slate-900">0 EGP</span>
                        </div>
                        <div id="discount-row" class="flex justify-between hidden">
                            <span class="text-slate-500">Discount</span>
                            <span id="discount-amount" class="font-bold text-rose-600">-0 EGP</span>
                        </div>
                    </div>

                    <div class="mt-5 pt-5 border-t border-slate-200 flex justify-between items-center">
                        <span class="text-slate-500 font-bold">Total</span>
                        <span id="total-price-display" class="text-2xl font-black text-slate-900">0 EGP</span>
                    </div>

                    {{-- Coupon --}}
                    <div class="mt-5">
                        <label class="text-xs font-bold text-slate-500 mb-1.5 block">Promo code</label>
                        <div class="flex gap-2">
                            <input type="text" id="coupon-code" class="flex-1 h-11 px-4 bg-slate-50 border border-slate-200 rounded-xl text-sm uppercase focus:border-violet-300 focus:bg-white outline-none transition-colors" placeholder="Enter code">
                            <button type="button" id="apply-coupon-btn" class="h-11 px-5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl transition-colors text-sm">Apply</button>
                        </div>
                        <p id="coupon-message" class="text-xs mt-2 hidden"></p>
                    </div>

                    <button type="button" id="confirm-btn" class="w-full mt-6 h-12 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-xl transition-colors shadow-lg shadow-violet-500/20">
                        Confirm Order
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
(function () {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const emptyEl = document.getElementById('checkout-empty');
    const contentEl = document.getElementById('checkout-content');
    const itemsEl = document.getElementById('checkout-items');
    const subtotalEl = document.getElementById('subtotal-display');
    const totalEl = document.getElementById('total-price-display');
    const discountRow = document.getElementById('discount-row');
    const discountAmountEl = document.getElementById('discount-amount');
    const couponInput = document.getElementById('coupon-code');
    const applyCouponBtn = document.getElementById('apply-coupon-btn');
    const couponMsg = document.getElementById('coupon-message');
    const confirmBtn = document.getElementById('confirm-btn');

    const welcomeCode = @json(site_setting('welcome_popup_discount_code', 'WELCOME10'));
    const welcomePercent = parseInt(@json(site_setting('welcome_popup_discount_percent', '10')));
    const freeThreshold = parseFloat(@json(site_setting('free_shipping_threshold', '2000')));
    const shippingRates = @json($shippingRates);

    let discountPercent = 0;
    let shippingCost = 0;

    if (cart.length === 0) {
        emptyEl.classList.remove('hidden');
        contentEl.classList.add('hidden');
    } else {
        renderItems();
    }

    function renderItems() {
        itemsEl.innerHTML = '';
        cart.forEach(item => {
            itemsEl.innerHTML += `
                <div class="flex gap-4">
                    <img src="${item.image}" alt="${item.name}" class="w-16 h-16 object-contain bg-slate-50 rounded-xl border border-slate-100 p-1">
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-sm text-slate-900 truncate">${item.name}</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Qty: ${item.quantity || 1}</p>
                        <p class="text-sm font-bold text-violet-600 mt-1">${((item.price || 0) * (item.quantity || 1)).toLocaleString()} EGP</p>
                    </div>
                </div>
            `;
        });
        updateTotals();
    }

    function subtotal() {
        return cart.reduce((sum, item) => sum + ((item.price || 0) * (item.quantity || 1)), 0);
    }

    function getDistinctCountries() {
        return [...new Set(shippingRates.map(r => r.country))].sort();
    }

    function populateCountries() {
        const select = document.getElementById('shipping-country');
        if (!select) return;
        select.innerHTML = '<option value="">Select country</option>';
        getDistinctCountries().forEach(country => {
            const opt = document.createElement('option');
            opt.value = country;
            opt.textContent = country;
            select.appendChild(opt);
        });
        const other = document.createElement('option');
        other.value = '__other__';
        other.textContent = "My country is not listed";
        select.appendChild(other);
    }

    function populateStates(country) {
        const select = document.getElementById('shipping-state');
        if (!select) return;
        select.innerHTML = '<option value="">Select state</option>';
        shippingRates.filter(r => r.country === country).forEach(r => {
            const opt = document.createElement('option');
            opt.value = r.id;
            opt.textContent = r.state + (r.city ? ' - ' + r.city : '');
            select.appendChild(opt);
        });
    }

    function calculateShipping() {
        const st = subtotal();
        if (st >= freeThreshold) {
            shippingCost = 0;
            return;
        }
        const stateSelect = document.getElementById('shipping-state');
        const rateId = stateSelect ? stateSelect.value : '';
        const rate = shippingRates.find(r => String(r.id) === String(rateId));
        shippingCost = rate ? parseFloat(rate.cost) : 0;
    }

    function updateTotals() {
        calculateShipping();
        const st = subtotal();
        const discount = Math.round(st * (discountPercent / 100));
        const total = Math.max(0, st + shippingCost - discount);
        subtotalEl.textContent = st.toLocaleString() + ' EGP';
        totalEl.textContent = total.toLocaleString() + ' EGP';

        const shippingEl = document.getElementById('shipping-display');
        if (shippingCost === 0 && st >= freeThreshold) {
            shippingEl.textContent = 'Free';
            shippingEl.className = 'font-bold text-emerald-600';
        } else {
            shippingEl.textContent = shippingCost.toLocaleString() + ' EGP';
            shippingEl.className = 'font-bold text-slate-900';
        }

        if (discount > 0) {
            discountRow.classList.remove('hidden');
            discountAmountEl.textContent = '-' + discount.toLocaleString() + ' EGP';
        } else {
            discountRow.classList.add('hidden');
        }
    }

    function applyCoupon() {
        const code = couponInput.value.trim().toUpperCase();
        if (!code) return;
        if (code === welcomeCode.toUpperCase()) {
            discountPercent = welcomePercent;
            couponMsg.textContent = `Coupon applied: ${welcomePercent}% OFF`;
            couponMsg.className = 'text-xs mt-2 text-emerald-600 font-bold';
        } else {
            discountPercent = 0;
            couponMsg.textContent = 'Invalid coupon code.';
            couponMsg.className = 'text-xs mt-2 text-rose-600 font-bold';
        }
        couponMsg.classList.remove('hidden');
        updateTotals();
    }

    applyCouponBtn.addEventListener('click', applyCoupon);
    couponInput.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); applyCoupon(); } });

    const countrySelect = document.getElementById('shipping-country');
    const stateSelect = document.getElementById('shipping-state');
    const unsupportedMsg = document.getElementById('unsupported-country');

    populateCountries();

    countrySelect?.addEventListener('change', () => {
        const country = countrySelect.value;
        if (country === '__other__' || !country) {
            stateSelect.innerHTML = '<option value="">Select state</option>';
            stateSelect.disabled = true;
            if (unsupportedMsg) unsupportedMsg.classList.remove('hidden');
            shippingCost = 0;
            updateTotals();
            return;
        }
        stateSelect.disabled = false;
        if (unsupportedMsg) unsupportedMsg.classList.add('hidden');
        populateStates(country);
        updateTotals();
    });

    stateSelect?.addEventListener('change', updateTotals);

    if (getDistinctCountries().length === 0) {
        stateSelect.disabled = true;
        if (unsupportedMsg) unsupportedMsg.classList.remove('hidden');
    }

    confirmBtn.addEventListener('click', () => {
        const form = document.getElementById('checkout-form');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        localStorage.removeItem('cart');
        window.location.href = @json(route('pages.payment-success'));
    });
})();
</script>
@endpush
