<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categoryMap = Category::whereNotNull('parent_id')
            ->get()
            ->mapWithKeys(fn ($cat) => [$cat->name => $cat->id]);

        $products = [
            [
                'name' => 'Dental Anesthetic Injection',
                'description' => 'A high-quality dental anesthetic injection containing Lidocaine, designed to provide fast and effective local anesthesia during dental procedures.',
                'short_description' => 'Fast-acting dental anesthetic with Lidocaine.',
                'price' => 700.00,
                'category' => 'Clinical Tools',
                'stock' => 3,
                'featured' => true,
                'legacy_image' => './imges/products/71RNtfCLkxL._AC_SX679_.jpg',
            ],
            [
                'name' => 'Soldering iron',
                'description' => 'A reliable soldering iron designed for assembling and repairing electronic circuits with precision and ease.',
                'short_description' => 'Reliable soldering iron for electronics projects.',
                'price' => 4775.00,
                'sale_price' => 4058.75,
                'category' => 'Electrical Engineering',
                'stock' => 12,
                'featured' => true,
                'legacy_image' => './imges/Engineering/Soldering iron.webp',
            ],
            [
                'name' => 'Endo training teeth',
                'description' => 'A high-quality endodontic training model teeth set designed for practicing root canal procedures.',
                'short_description' => 'Endodontic training teeth for clinical practice.',
                'price' => 1299.00,
                'category' => 'Training Tools',
                'stock' => 10,
                'featured' => true,
                'legacy_image' => './imges/products/PHOTO-2026-04-12-14-23-32.jpg',
            ],
            [
                'name' => 'Oscilloscope',
                'description' => 'A powerful electronic testing device used to visualize and analyze electrical signals in real-time.',
                'short_description' => 'Electronic signal analysis oscilloscope.',
                'price' => 7599.00,
                'category' => 'Electrical Engineering',
                'stock' => 0,
                'featured' => false,
                'legacy_image' => './imges/Engineering/Oscilloscope.jpeg',
            ],
            [
                'name' => 'Basic paint sets',
                'description' => 'A beginner-friendly basic paint set designed for students to practice color mixing and painting techniques.',
                'short_description' => 'Beginner paint set for art students.',
                'price' => 1359.00,
                'sale_price' => 1155.15,
                'category' => 'Painting',
                'stock' => 12,
                'featured' => true,
                'legacy_image' => './imges/Fine Arts/Basic paint sets.webp',
            ],
            [
                'name' => 'Dental Tweezers',
                'description' => 'A precision dental instrument used for holding and placing small materials during dental procedures.',
                'short_description' => 'Precision dental tweezers.',
                'price' => 399.00,
                'category' => 'Diagnostic Tools',
                'stock' => 20,
                'featured' => false,
                'legacy_image' => './imges/products/tweezerwal4_1024x.webp',
            ],
            [
                'name' => 'Digital multimeter',
                'description' => 'A versatile digital multimeter designed to measure voltage, current, and resistance with high accuracy.',
                'short_description' => 'Accurate digital multimeter for labs.',
                'price' => 599.00,
                'category' => 'Electrical Engineering',
                'stock' => 34,
                'featured' => false,
                'legacy_image' => './imges/Engineering/Digital multimeter.webp',
            ],
            [
                'name' => 'Professional Stethoscope',
                'description' => 'A high-quality stethoscope designed for accurate auscultation of heart, lung, and body sounds.',
                'short_description' => 'Professional grade stethoscope.',
                'price' => 1599.00,
                'category' => 'Diagnostic Tools',
                'stock' => 34,
                'featured' => true,
                'legacy_image' => './imges/Nursing/stethoscope-isolated-white-surface-2.jpg',
            ],
            [
                'name' => 'Electronic theodolite',
                'description' => 'A high-precision electronic surveying instrument used for measuring horizontal and vertical angles.',
                'short_description' => 'Precision surveying theodolite.',
                'price' => 1299.00,
                'category' => 'Civil Engineering',
                'stock' => 5,
                'featured' => true,
                'legacy_image' => './imges/Engineering/s-l1600.webp',
            ],
            [
                'name' => 'Professional brushes',
                'description' => 'A set of high-quality professional brushes designed for detailed painting and artistic expression.',
                'short_description' => 'Professional art brush set.',
                'price' => 1299.00,
                'category' => 'Sculpture',
                'stock' => 8,
                'featured' => false,
                'legacy_image' => './imges/Fine Arts/Professional brushes.webp',
            ],
        ];

        foreach ($products as $index => $data) {
            $categoryId = $categoryMap[$data['category']] ?? Category::first()?->id;

            $product = Product::create([
                'category_id' => $categoryId,
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'sku' => 'SKU-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                'short_description' => $data['short_description'],
                'description' => $data['description'],
                'price' => $data['price'],
                'sale_price' => $data['sale_price'] ?? null,
                'stock' => $data['stock'],
                'featured' => $data['featured'],
                'status' => true,
            ]);

            if (! empty($data['legacy_image'])) {
                $product->images()->create([
                    'image' => ltrim($data['legacy_image'], './'),
                    'medium' => ltrim($data['legacy_image'], './'),
                    'thumb' => ltrim($data['legacy_image'], './'),
                    'large' => ltrim($data['legacy_image'], './'),
                    'sort_order' => 1,
                ]);
            }
        }
    }
}
