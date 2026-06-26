@extends('account.layout')
@section('account_content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-violet-50/50 to-transparent">
        <h1 class="font-black text-slate-900 flex items-center gap-2">
            <i class="fa-solid fa-receipt text-violet-600"></i> طلباتي
        </h1>
        <span class="text-xs font-bold text-slate-500">{{ $orders->total() }} طلب</span>
    </div>
    <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[700px]">
        <thead class="bg-slate-50 text-xs text-slate-600"><tr>
            <th class="p-3 text-right">رقم الطلب</th><th class="p-3">العناصر</th><th class="p-3">الإجمالي</th><th class="p-3">الحالة</th><th class="p-3">التاريخ</th><th class="p-3"></th>
        </tr></thead>
        <tbody>
            @forelse($orders as $o)
            <tr class="border-t border-slate-100 hover:bg-violet-50/40 transition">
                <td class="p-3 font-mono font-bold text-violet-700">{{ $o->order_number }}</td>
                <td class="p-3 text-center">{{ $o->items_count }}</td>
                <td class="p-3 text-center font-bold">{{ number_format($o->total, 2) }} {{ $o->currency }}</td>
                <td class="p-3 text-center"><span class="text-xs px-2 py-1 rounded-full bg-{{ $o->statusBadgeColor() }}-50 text-{{ $o->statusBadgeColor() }}-700 font-bold">{{ $o->statusLabel() }}</span></td>
                <td class="p-3 text-center text-xs text-slate-500">{{ $o->created_at->format('Y-m-d H:i') }}</td>
                <td class="p-3 text-center"><a href="{{ route('account.orders.show', $o) }}" class="inline-flex items-center gap-1 text-violet-600 text-xs font-bold hover:underline">تفاصيل <i class="fa-solid fa-arrow-left text-[9px]"></i></a></td>
            </tr>
            @empty
            <tr><td colspan="6" class="p-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-violet-50 text-violet-600 grid place-items-center text-2xl mx-auto mb-3"><i class="fa-solid fa-box-open"></i></div>
                <p class="text-slate-500">لا توجد طلبات.</p>
            </td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection
