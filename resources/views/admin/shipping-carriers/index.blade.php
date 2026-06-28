@extends('admin.layouts.app')

@section('title', 'Shipping Carriers')

@section('content')
<div class="p-6 space-y-6" x-data="{ showForm: false, editing: null }">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-800">Shipping Carriers</h1>
        <button @click="showForm = true; editing = null" class="px-4 py-2 rounded-xl bg-violet-600 text-white text-sm font-semibold hover:bg-violet-700">
            <i class="fa-solid fa-plus mr-2"></i> Add Carrier
        </button>
    </div>

    {{-- Aramex one-click install --}}
    <div class="rounded-2xl border border-amber-200 bg-gradient-to-r from-amber-50 to-orange-50 p-5 flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-orange-500 text-white flex items-center justify-center text-xl font-bold">A</div>
            <div>
                <h3 class="font-bold text-slate-800">Aramex</h3>
                <p class="text-xs text-slate-600">
                    @if(!$aramexInstalled)
                        <span class="text-rose-600 font-semibold">SDK not installed.</span> Run: <code class="bg-white px-2 py-0.5 rounded">composer require octw/aramex</code>
                    @elseif(!$aramexConfigured)
                        <span class="text-amber-700 font-semibold">SDK installed but not configured.</span> Publish config: <code class="bg-white px-2 py-0.5 rounded">php artisan vendor:publish --provider="Octw\Aramex\AramexServiceProvider"</code>
                    @else
                        <span class="text-emerald-700 font-semibold">✓ Ready.</span> Click the button to create/update the Aramex carrier.
                    @endif
                </p>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.shipping-carriers.install-aramex') }}">
            @csrf
            <button type="submit"
                style="background-color:#ea580c;color:#ffffff;"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold shadow-md hover:shadow-lg hover:brightness-110 transition border border-orange-700">
                <i class="fa-solid fa-bolt"></i>
                <span>Enable Aramex</span>
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700">{{ session('error') }}</div>
    @endif

    {{-- Form Modal --}}
    <div x-show="showForm" x-transition class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="showForm = false">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto">
            <h2 class="text-lg font-bold text-slate-800 mb-4" x-text="editing ? 'Edit Carrier' : 'Add Carrier'"></h2>
            <form :action="editing ? `/admin/shipping-carriers/${editing.id}` : '{{ route('admin.shipping-carriers.store') }}'" method="POST" class="space-y-4">
                @csrf
                <template x-if="editing">@method('PUT')</template>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Carrier Name *</label>
                    <input type="text" name="name" :value="editing?.name" required class="w-full rounded-xl border-slate-200">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Unique Code *</label>
                    <input type="text" name="code" :value="editing?.code" required class="w-full rounded-xl border-slate-200" placeholder="dhl, aramex, bosta...">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tracking URL (use {tracking} for the number)</label>
                    <input type="text" name="tracking_url_template" :value="editing?.tracking_url_template" class="w-full rounded-xl border-slate-200" placeholder="https://example.com/track/{tracking}">
                </div>

                <div class="rounded-xl border border-violet-100 bg-violet-50/40 p-3 space-y-3">
                    <p class="text-xs font-bold text-violet-700"><i class="fa-solid fa-plug"></i> API Settings for Auto-tracking</p>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tracking API URL (must include {tracking})</label>
                        <input type="text" name="api_endpoint" :value="editing?.api_endpoint" class="w-full rounded-xl border-slate-200" placeholder="https://api.carrier.com/v1/tracking/{tracking}">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">API Key</label>
                            <input type="text" name="api_key" :value="editing?.api_key" class="w-full rounded-xl border-slate-200">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Webhook Secret</label>
                            <input type="text" name="webhook_secret" :value="editing?.webhook_secret" class="w-full rounded-xl border-slate-200" placeholder="Optional — secures the webhook">
                        </div>
                    </div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="auto_track" value="1" :checked="editing?.auto_track" class="rounded">
                        <span class="text-xs font-semibold text-slate-700">Auto-update when order is opened</span>
                    </label>
                    <p class="text-[11px] text-slate-500 leading-relaxed">
                        Webhook URL to receive updates from the carrier:<br>
                        <code class="text-[10px] bg-white px-2 py-1 rounded border" x-text="`{{ url('/api/shipping') }}/${editing?.code ?? '<code>'}/webhook`"></code>
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Contact Phone</label>
                        <input type="text" name="contact_phone" :value="editing?.contact_phone" class="w-full rounded-xl border-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                        <input type="email" name="contact_email" :value="editing?.contact_email" class="w-full rounded-xl border-slate-200">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Default Cost</label>
                        <input type="number" step="0.01" name="default_cost" :value="editing?.default_cost ?? 0" class="w-full rounded-xl border-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Display Order</label>
                        <input type="number" name="sort_order" :value="editing?.sort_order ?? 0" class="w-full rounded-xl border-slate-200">
                    </div>
                </div>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" :checked="editing ? editing.is_active : true" class="rounded">
                    <span class="text-sm font-semibold text-slate-700">Active</span>
                </label>
                <div class="flex gap-2 pt-4 border-t">
                    <button type="submit" class="flex-1 px-4 py-2 rounded-xl bg-violet-600 text-white font-semibold hover:bg-violet-700">Save</button>
                    <button type="button" @click="showForm = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Code</th>
                    <th class="px-4 py-3 text-left">Phone</th>
                    <th class="px-4 py-3 text-left">Cost</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Orders</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($carriers as $c)
                    <tr>
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $c->name }}</td>
                        <td class="px-4 py-3 text-slate-500 font-mono">{{ $c->code }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $c->contact_phone ?: '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ number_format($c->default_cost, 2) }}</td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.shipping-carriers.toggle', $c) }}">
                                @csrf @method('PATCH')
                                <button class="px-3 py-1 rounded-full text-xs font-bold {{ $c->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                                    {{ $c->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-slate-500">{{ $c->orders()->count() }}</td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <button @click="editing = @js($c); showForm = true" class="px-3 py-1 rounded-lg bg-sky-100 text-sky-700 text-xs font-semibold hover:bg-sky-200">Edit</button>
                                <form method="POST" action="{{ route('admin.shipping-carriers.destroy', $c) }}" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1 rounded-lg bg-rose-100 text-rose-700 text-xs font-semibold hover:bg-rose-200">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-12 text-slate-400">No shipping carriers yet. Add one to make it available on the orders page.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
