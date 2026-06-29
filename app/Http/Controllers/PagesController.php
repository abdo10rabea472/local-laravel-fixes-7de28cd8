<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class PagesController extends Controller
{
    public function about()
    {
        $page = Page::bySlug('about')->active()->first();

        $stats = Cache::remember('pages.about.stats', 600, function () {
            return [
                'products'   => Product::where('status', true)->count(),
                'categories' => Category::count(),
                'years'      => max(1, now()->year - 2020),
                'customers'  => (int) (\App\Models\User::count() * 1.2 + 50),
            ];
        });

        $about = [
            'hero' => [
                'title'    => __('app.about_hero_title'),
                'subtitle' => __('app.about_hero_subtitle'),
            ],
            'story' => [
                'title' => __('app.about_story_title'),
                'p1'    => __('app.about_story_p1', ['years' => $stats['years']]),
                'p2'    => __('app.about_story_p2'),
            ],
            'stats' => [
                ['label' => __('app.about_stat_products'),   'value' => $stats['products'] . '+',     'color' => 'violet'],
                ['label' => __('app.about_stat_categories'), 'value' => (string) $stats['categories'], 'color' => 'indigo'],
                ['label' => __('app.about_stat_customers'),  'value' => $stats['customers'] . '+',    'color' => 'emerald'],
                ['label' => __('app.about_stat_years'),      'value' => $stats['years'] . '+',        'color' => 'amber'],
            ],
            'cards' => [
                ['icon' => 'fa-bullseye',  'title' => __('app.about_mission_title'), 'desc' => __('app.about_mission_desc'), 'color' => 'violet'],
                ['icon' => 'fa-eye',       'title' => __('app.about_vision_title'),  'desc' => __('app.about_vision_desc'),  'color' => 'indigo'],
                ['icon' => 'fa-handshake', 'title' => __('app.about_values_title'),  'desc' => __('app.about_values_desc'),  'color' => 'emerald'],
            ],
            'team_title' => __('app.about_team_title'),
            'team' => [
                ['name' => __('app.about_team_member1_name'), 'role' => __('app.about_team_member1_role')],
                ['name' => __('app.about_team_member2_name'), 'role' => __('app.about_team_member2_role')],
                ['name' => __('app.about_team_member3_name'), 'role' => __('app.about_team_member3_role')],
                ['name' => __('app.about_team_member4_name'), 'role' => __('app.about_team_member4_role')],
            ],
        ];

        if ($page && trim((string) $page->content) !== '') {
            $decoded = json_decode((string) $page->content, true);
            if (is_array($decoded)) {
                foreach (['hero', 'story'] as $sec) {
                    foreach ($decoded[$sec] ?? [] as $k => $v) {
                        if ($v !== '' && $v !== null) {
                            $about[$sec][$k] = $v;
                        }
                    }
                }
                if (!empty($decoded['team_title'])) {
                    $about['team_title'] = $decoded['team_title'];
                }
                foreach (['stats', 'cards'] as $sec) {
                    foreach ($decoded[$sec] ?? [] as $i => $row) {
                        if (!isset($about[$sec][$i]) || !is_array($row)) continue;
                        foreach ($row as $k => $v) {
                            if ($v !== '' && $v !== null) {
                                $about[$sec][$i][$k] = $v;
                            }
                        }
                    }
                }
                if (!empty($decoded['team']) && is_array($decoded['team'])) {
                    $team = [];
                    foreach ($decoded['team'] as $m) {
                        if (!is_array($m)) continue;
                        $name = (string) ($m['name'] ?? '');
                        $role = (string) ($m['role'] ?? '');
                        if ($name !== '' || $role !== '') {
                            $team[] = ['name' => $name, 'role' => $role];
                        }
                    }
                    if (!empty($team)) {
                        $about['team'] = $team;
                    }
                }
            }
        }

        return view('pages.about', compact('about', 'page'));
    }
}
