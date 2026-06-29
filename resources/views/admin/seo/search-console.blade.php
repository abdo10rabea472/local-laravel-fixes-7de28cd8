@extends('admin.layouts.app')

@section('title', 'Search Console')

@section('content')
<div class="p-6 space-y-6" dir="rtl">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">أداء البحث (Google Search Console)</h1>
            <p class="text-sm text-slate-500 mt-1">آخر {{ $days }} يوم — البيانات محدّثة كل 30 دقيقة (cache).</p>
        </div>
        <div class="flex gap-2">
            @foreach([7,28,90] as $d)
                <a href="{{ route('admin.seo.gsc', ['days' => $d]) }}"
                   class="px-3 py-1.5 rounded-lg text-sm font-semibold {{ $days==$d ? 'bg-violet-600 text-white' : 'bg-white text-slate-600 border border-slate-200' }}">
                   {{ $d }} يوم
                </a>
            @endforeach
        </div>
    </div>

    @if(!$gsc->isConfigured() || isset($byDate['error']))
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
            <h3 class="font-bold text-amber-800 mb-2">إعداد مطلوب</h3>
            <p class="text-sm text-amber-700 mb-3">لتفعيل التكامل احتاج 3 خطوات:</p>
            <ol class="list-decimal pr-5 text-sm text-amber-800 space-y-1">
                <li>أنشئ Service Account من <a class="underline" target="_blank" href="https://console.cloud.google.com/iam-admin/serviceaccounts">Google Cloud Console</a> وفعّل Search Console API.</li>
                <li>نزّل ملف JSON، وأضف بريد الـ service account كمستخدم في Search Console > Settings > Users and permissions.</li>
                <li>افتح <a class="underline" href="{{ route('admin.settings.index') }}">إعدادات الموقع</a> وضع:
                    <code class="bg-amber-100 px-2 rounded">gsc_site_url</code> (مثل <code>https://yoursite.com/</code>) و
                    <code class="bg-amber-100 px-2 rounded">gsc_service_account_json</code> (محتوى الملف).
                </li>
            </ol>
            @if(isset($byDate['error']) && $byDate['error'] !== 'not_configured')
                <div class="mt-4 text-xs bg-white border border-amber-200 rounded p-3 text-slate-700">
                    <strong>API Error:</strong> {{ $byDate['error'] }}
                    @if(isset($byDate['body'])) <pre class="mt-2 whitespace-pre-wrap">{{ \Illuminate\Support\Str::limit($byDate['body'], 500) }}</pre> @endif
                </div>
            @endif
        </div>
    @else
        {{-- KPIs --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php $kpis = [
                ['نقرات', number_format($totals['clicks']), 'mouse-pointer', 'violet'],
                ['ظهور', number_format($totals['impressions']), 'eye', 'indigo'],
                ['CTR', $totals['ctr'].'%', 'percent', 'emerald'],
                ['متوسط الترتيب', $totals['position'], 'ranking-star', 'amber'],
            ]; @endphp
            @foreach($kpis as [$label,$val,$icon,$color])
                <div class="bg-white rounded-xl border border-slate-200 p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500">{{ $label }}</span>
                        <i class="fa-solid fa-{{ $icon }} text-{{ $color }}-500"></i>
                    </div>
                    <div class="text-2xl font-black text-slate-800 mt-2">{{ $val }}</div>
                </div>
            @endforeach
        </div>

        {{-- Chart --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h3 class="font-bold text-slate-800 mb-3">نقرات وظهور حسب اليوم</h3>
            <canvas id="gscChart" height="80"></canvas>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            {{-- Top queries --}}
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <h3 class="font-bold text-slate-800 mb-3">أعلى الكلمات المفتاحية</h3>
                <table class="w-full text-sm">
                    <thead class="text-slate-500 text-xs"><tr>
                        <th class="text-start py-2">الكلمة</th><th>نقرات</th><th>ظهور</th><th>CTR</th><th>الترتيب</th>
                    </tr></thead>
                    <tbody>
                    @foreach($byQuery['rows'] ?? [] as $r)
                        <tr class="border-t border-slate-100">
                            <td class="py-2 text-slate-700 font-medium">{{ $r['keys'][0] ?? '' }}</td>
                            <td class="text-center">{{ $r['clicks'] }}</td>
                            <td class="text-center">{{ $r['impressions'] }}</td>
                            <td class="text-center">{{ round(($r['ctr'] ?? 0) * 100, 1) }}%</td>
                            <td class="text-center">{{ round($r['position'] ?? 0, 1) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Top pages --}}
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <h3 class="font-bold text-slate-800 mb-3">أعلى الصفحات</h3>
                <table class="w-full text-sm">
                    <thead class="text-slate-500 text-xs"><tr>
                        <th class="text-start py-2">الصفحة</th><th>نقرات</th><th>ظهور</th><th>CTR</th>
                    </tr></thead>
                    <tbody>
                    @foreach($byPage['rows'] ?? [] as $r)
                        <tr class="border-t border-slate-100">
                            <td class="py-2"><a class="text-violet-600 hover:underline truncate inline-block max-w-[260px]" href="{{ $r['keys'][0] ?? '#' }}" target="_blank">{{ $r['keys'][0] ?? '' }}</a></td>
                            <td class="text-center">{{ $r['clicks'] }}</td>
                            <td class="text-center">{{ $r['impressions'] }}</td>
                            <td class="text-center">{{ round(($r['ctr'] ?? 0) * 100, 1) }}%</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            (function () {
                const rows = @json($byDate['rows'] ?? []);
                const labels = rows.map(r => r.keys[0]);
                const clicks = rows.map(r => r.clicks);
                const impressions = rows.map(r => r.impressions);
                const ctx = document.getElementById('gscChart');
                if (!ctx || !rows.length) return;
                new Chart(ctx, {
                    type: 'line',
                    data: { labels, datasets: [
                        { label: 'نقرات', data: clicks, borderColor: '#7c3aed', backgroundColor: 'rgba(124,58,237,.1)', tension: .3, yAxisID: 'y' },
                        { label: 'ظهور', data: impressions, borderColor: '#6366f1', backgroundColor: 'rgba(99,102,241,.1)', tension: .3, yAxisID: 'y1' },
                    ]},
                    options: { responsive: true, interaction: { mode: 'index', intersect: false },
                        scales: { y: { position: 'left' }, y1: { position: 'right', grid: { drawOnChartArea: false } } } }
                });
            })();
        </script>
    @endif
</div>
@endsection
