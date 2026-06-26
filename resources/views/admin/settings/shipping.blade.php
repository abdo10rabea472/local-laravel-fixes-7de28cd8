@extends('admin.settings.layout')

@section('settings-content')
<div class="space-y-6" x-data="{
    showModal: false,
    isEdit: false,
    form: { id: null, country: 'Egypt', state: '', city: '', cost: 0, position: 0, status: true },
    openCreate() {
        this.isEdit = false;
        this.form = { id: null, country: 'Egypt', state: '', city: '', cost: 0, position: 0, status: true };
        this.showModal = true;
    },
    openEdit(item) {
        this.isEdit = true;
        this.form = { ...item, status: !!item.status };
        this.showModal = true;
    }
}">
    {{-- Free shipping threshold --}}
    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="text-base font-bold text-slate-800">إعدادات الشحن المجاني</h3>
            <p class="text-xs text-slate-500 mt-1">تحديد الحد الأدنى لطلب الشحن المجاني.</p>
        </div>
        <form method="POST" action="{{ route('admin.settings.shipping.threshold') }}" class="p-6">
            @csrf @method('PUT')
            <div class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="space-y-2 flex-1 w-full">
                    <label class="text-xs font-bold text-slate-500">الحد الأدنى للشحن المجاني (EGP)</label>
                    <input type="number" min="0" step="0.01" name="free_shipping_threshold" value="{{ $freeThreshold }}" class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm">
                </div>
                <button type="submit" class="h-11 px-6 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl transition-colors shadow-md shadow-emerald-500/20">
                    حفظ
                </button>
            </div>
        </form>
    </div>

    {{-- Shipping rates --}}
    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 px-6 py-4 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-800">أسعار الشحن</h3>
                <p class="text-xs text-slate-500 mt-1">تحديد سعر الشحن لكل محافظة/مدينة.</p>
            </div>
            <button @click="openCreate()" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm px-5 py-2.5 rounded-xl shadow-md shadow-emerald-500/20 transition-colors">
                <i class="fa-solid fa-plus ml-2"></i> إضافة سعر
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs font-bold uppercase">
                    <tr>
                        <th class="px-6 py-3 text-right">الدولة</th>
                        <th class="px-6 py-3 text-right">المحافظة / الولاية</th>
                        <th class="px-6 py-3 text-right">المدينة</th>
                        <th class="px-6 py-3 text-right">التكلفة</th>
                        <th class="px-6 py-3 text-right">الحالة</th>
                        <th class="px-6 py-3 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rates as $rate)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 font-semibold text-slate-800">{{ $rate->country }}</td>
                        <td class="px-6 py-4 text-slate-700">{{ $rate->state }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $rate->city ?: '—' }}</td>
                        <td class="px-6 py-4 font-bold text-violet-600">{{ number_format($rate->cost, 2) }} EGP</td>
                        <td class="px-6 py-4">
                            @if($rate->status)
                                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-1 rounded-full border border-emerald-200">نشط</span>
                            @else
                                <span class="text-xs font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded-full">معطل</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <button @click="openEdit(@js($rate))" class="text-violet-600 hover:text-violet-800">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.settings.shipping.destroy', $rate) }}" onsubmit="return confirm('حذف سعر الشحن؟')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-rose-500 hover:text-rose-700">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                            <i class="fa-solid fa-truck-fast text-4xl text-slate-300 mb-3"></i>
                            <p>لا توجد أسعار شحن مضافة.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rates->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $rates->links() }}
        </div>
        @endif
    </div>

    {{-- Modal --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg p-6" @click.outside="showModal = false">
            <h4 class="text-lg font-bold mb-4" x-text="isEdit ? 'تعديل سعر الشحن' : 'إضافة سعر شحن'"></h4>
            <form :action="isEdit ? '{{ url('admin/settings/shipping') }}/' + form.id : '{{ route('admin.settings.shipping.store') }}'" method="POST" class="space-y-4">
                @csrf
                <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500">الدولة</label>
                        <input type="text" name="country" x-model="form.country" required class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500">المحافظة / الولاية *</label>
                        <input type="text" name="state" x-model="form.state" required class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500">المدينة (اختياري)</label>
                        <input type="text" name="city" x-model="form.city" class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500">التكلفة (EGP) *</label>
                        <input type="number" step="0.01" min="0" name="cost" x-model="form.cost" required class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500">الترتيب</label>
                        <input type="number" name="position" x-model="form.position" class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm">
                    </div>
                    <div class="flex items-center gap-2 h-11 mt-6">
                        <input type="checkbox" name="status" value="1" x-model="form.status" class="rounded">
                        <label class="text-sm font-semibold text-slate-700">نشط</label>
                    </div>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 h-11 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-2xl transition-colors">
                        حفظ
                    </button>
                    <button type="button" @click="showModal = false" class="h-11 px-6 bg-slate-100 rounded-2xl font-bold">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
