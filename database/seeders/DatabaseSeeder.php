<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User in admins table
        \App\Models\Admin::create([
            'name' => 'Admin Manager',
            'email' => 'admin@uni.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);

        // Create Regular Test User
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);

        // Seed Categories & Products
        $this->call(CategorySeeder::class);
        $this->call(ProductSeeder::class);

        // Seed Default Static Pages
        $this->call(PageSeeder::class);

        // Seed Default Header Menu
        $this->call(HeaderMenuSeeder::class);
    }
}
