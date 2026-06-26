<?php

namespace Database\Seeders;

use App\Models\HeaderMenuItem;
use Illuminate\Database\Seeder;

class HeaderMenuSeeder extends Seeder
{
    public function run(): void
    {
        if (HeaderMenuItem::count() > 0) {
            return;
        }

        $shopByCollege = HeaderMenuItem::create([
            'title' => 'Shop by College',
            'url' => '__colleges__',
            'icon' => 'fa-building-columns',
            'position' => 0,
            'status' => true,
        ]);

        HeaderMenuItem::create([
            'title' => 'All Products',
            'url' => '/products',
            'icon' => 'fa-box',
            'position' => 1,
            'status' => true,
        ]);

        HeaderMenuItem::create([
            'title' => 'Featured',
            'url' => '/#featured',
            'icon' => 'fa-star',
            'position' => 2,
            'status' => true,
        ]);
    }
}
