<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('discount_percentage')->default(0);
            $table->string('image_path')->nullable();
            $table->string('college'); // Medicine, Engineering, Fine Arts, Nursing, Pharmacy, Sciences, Computer Science
            $table->string('category')->nullable(); // Clinical Tools, Painting, etc.
            $table->integer('stock')->default(10);
            $table->string('status')->default('in_stock');
            $table->decimal('rating', 2, 1)->default(4.8);
            $table->integer('reviews_count')->default(120);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
