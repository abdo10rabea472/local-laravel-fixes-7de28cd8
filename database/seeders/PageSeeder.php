<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'faqs',
                'title' => 'Frequently Asked Questions',
                'seo_title' => 'FAQs | UNI-LAB MARKET',
                'seo_description' => 'Frequently asked questions about ordering, shipping, returns, and payments.',
            ],
            [
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'seo_title' => 'Privacy Policy | UNI-LAB MARKET',
                'seo_description' => 'How we collect, use, and protect your personal information.',
            ],
            [
                'slug' => 'returns-refunds',
                'title' => 'Returns & Refunds',
                'seo_title' => 'Returns & Refunds | UNI-LAB MARKET',
                'seo_description' => 'Our clear and fair return and refund policy.',
            ],
            [
                'slug' => 'payment-success',
                'title' => 'Your tools are on the way!',
                'content' => 'Thank you for shopping with UNI-LAB MARKET.',
                'seo_title' => 'Order Confirmed | UNI-LAB MARKET',
                'seo_description' => 'Your order was placed successfully.',
            ],
        ];

        foreach ($pages as $page) {
            Page::firstOrCreate(['slug' => $page['slug']], $page);
        }

        // Remove checkout page if it exists
        Page::where('slug', 'checkout')->delete();
    }
}
