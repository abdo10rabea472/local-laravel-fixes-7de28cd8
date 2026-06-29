<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SearchConsoleService;
use Illuminate\Http\Request;

class SearchConsoleController extends Controller
{
    public function index(Request $request, SearchConsoleService $gsc)
    {
        $days = (int) $request->integer('days', 28);
        $days = in_array($days, [7, 28, 90], true) ? $days : 28;

        $byDate    = $gsc->isConfigured() ? $gsc->searchAnalytics($days, ['date'], 200) : ['error' => 'not_configured'];
        $byQuery   = $gsc->isConfigured() ? $gsc->searchAnalytics($days, ['query'], 25) : ['error' => 'not_configured'];
        $byPage    = $gsc->isConfigured() ? $gsc->searchAnalytics($days, ['page'], 25)  : ['error' => 'not_configured'];
        $byCountry = $gsc->isConfigured() ? $gsc->searchAnalytics($days, ['country'], 10) : ['error' => 'not_configured'];

        // Totals from byDate rows
        $totals = ['clicks' => 0, 'impressions' => 0, 'ctr' => 0, 'position' => 0];
        $rows = $byDate['rows'] ?? [];
        if (!empty($rows)) {
            foreach ($rows as $r) {
                $totals['clicks'] += $r['clicks'] ?? 0;
                $totals['impressions'] += $r['impressions'] ?? 0;
            }
            $totals['ctr'] = $totals['impressions'] > 0
                ? round(($totals['clicks'] / $totals['impressions']) * 100, 2) : 0;
            $totals['position'] = round(collect($rows)->avg('position') ?? 0, 1);
        }

        return view('admin.seo.search-console', compact('byDate', 'byQuery', 'byPage', 'byCountry', 'totals', 'days', 'gsc'));
    }
}
