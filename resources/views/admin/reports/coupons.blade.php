@extends('admin.layouts.app')

@section('title', 'Coupon Reports')

@section('content')
<div class="p-6 space-y-6">
    <h1 class="text-2xl font-bold text-slate-800">Coupon Reports</h1>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white border rounded-2xl p-5">
            <p class="text-xs font-bold text-slate-500">Total Redemptions</p>
            <h3 class="text-2xl font-black text-violet-600 mt-2">{{ (int) $totals->redemptions }}</h3>
        </div>
        <div class="bg-white border rounded-2xl p-5">
            <p class="text-xs font-bold text-slate-500">Total Discounts Given</p>
            <h3 class="text-2xl font-black text-rose-600 mt-2">{{ number_format((float) $totals->discount, 2) }} <span class="text-xs">EGP</span></h3>
        </div>
        <div class="bg-white border rounded-2xl p-5">
            <p class="text-xs font-bold text-slate-500">Related Order Revenue</p>
            <h3 class="text-2xl font-black text-emerald-600 mt-2">{{ number_format((float) $totals->revenue, 2) }} <span class="text-xs">EGP</span></h3>
        </div>
    </div>

    <div class="bg-white border rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b">
            <h3 class="font-bold text-slate-800">All Coupons</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-2 text-left">Code</th>
                    <th class="px-4 py-2 text-left">Value</th>
                    <th class="px-4 py-2 text-left">Uses</th>
                    <th class="px-4 py-2 text-left">Total Discount</th>
                    <th class="px-4 py-2 text-left">Order Revenue</th>
                    <th class="px-4 py-2 text-left">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($coupons as $c)
                    <tr>
                        <td class="px-4 py-3 font-mono font-bold text-slate-800">{{ $c->code }}</td>
                        <td class="px-4 py-3">
                            @if($c->type === 'percent')
                                {{ rtrim(rtrim(number_format($c->value, 2), '0'), '.') }}%
                            @else
                                {{ number_format($c->value, 2) }} EGP
                            @endif
                        </td>
                        <td class="px-4 py-3 font-bold text-violet-600">{{ $c->redemptions_count }} {{ $c->usage_limit ? "/ {$c->usage_limit}" : '' }}</td>
                        <td class="px-4 py-3 text-rose-600 font-semibold">{{ number_format((float) ($c->total_discount ?? 0), 2) }}</td>
                        <td class="px-4 py-3 text-emerald-600 font-semibold">{{ number_format((float) ($c->total_revenue ?? 0), 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold {{ $c->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $c->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-12 text-slate-400">No coupons</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t">{{ $coupons->links() }}</div>
    </div>
</div>
@endsection
