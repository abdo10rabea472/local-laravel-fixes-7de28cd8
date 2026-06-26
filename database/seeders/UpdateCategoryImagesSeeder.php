<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class UpdateCategoryImagesSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'medicine' => [
                'image' => 'imges/index/111.png',
                'banner' => 'imges/close-up-dentist-instruments (2).jpg',
                'primary_color' => '#10b981',
                'secondary_color' => '#06b6d4',
                'description' => 'High-quality medical instruments and clinical supplies.',
            ],
            'engineering' => [
                'image' => 'imges/index/222.png',
                'banner' => 'imges/bg_banner3.jpg',
                'primary_color' => '#3b82f6',
                'secondary_color' => '#06b6d4',
                'description' => 'Professional tools for all engineering disciplines.',
            ],
            'fine-arts' => [
                'image' => 'imges/index/333.png',
                'banner' => 'imges/Fine Arts/Basic paint sets.webp',
                'primary_color' => '#f43f5e',
                'secondary_color' => '#a855f7',
                'description' => 'Premium supplies for artists and design students.',
            ],
            'nursing' => [
                'image' => 'imges/index/444.png',
                'banner' => 'imges/Nursing/stethoscope-isolated-white-surface-2.jpg',
                'primary_color' => '#ec4899',
                'secondary_color' => '#f43f5e',
                'description' => 'Clinical training equipment and patient care tools.',
            ],
            'pharmacy' => [
                'image' => 'imges/index/555.png',
                'banner' => 'imges/products/PHOTO-2026-04-12-14-23-32.jpg',
                'primary_color' => '#8b5cf6',
                'secondary_color' => '#a855f7',
                'description' => 'Laboratory glassware and pharmaceutical lab equipment.',
            ],
            'sciences' => [
                'image' => 'imges/index/666.png',
                'banner' => 'imges/products/Anatomy Model Set.jpg',
                'primary_color' => '#14b8a6',
                'secondary_color' => '#0ea5e9',
                'description' => 'Scientific instruments and laboratory essentials.',
            ],
            'computer-science' => [
                'image' => 'imges/index/777.png',
                'banner' => 'imges/products/Arduino starter kit.webp',
                'primary_color' => '#6366f1',
                'secondary_color' => '#8b5cf6',
                'description' => 'Hardware, networking, and computing lab tools.',
            ],
        ];

        foreach ($map as $slug => $data) {
            Category::where('slug', $slug)->whereNull('parent_id')->update($data);
        }
    }
}
