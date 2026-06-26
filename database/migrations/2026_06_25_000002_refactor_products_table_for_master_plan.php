<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'discount_percentage',
                'image_path',
                'college',
                'category',
                'rating',
                'reviews_count',
                'is_featured',
                'status',
            ]);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('id')->constrained('categories')->nullOnDelete();
            $table->string('slug')->unique()->after('name');
            $table->string('sku')->nullable()->unique()->after('slug');
            $table->text('short_description')->nullable()->after('sku');
            $table->decimal('sale_price', 10, 2)->nullable()->after('price');
            $table->boolean('featured')->default(false)->after('stock');
            $table->string('seo_title')->nullable()->after('featured');
            $table->text('seo_description')->nullable()->after('seo_title');
            $table->text('seo_keywords')->nullable()->after('seo_description');
            $table->string('canonical_url')->nullable()->after('seo_keywords');
            $table->string('og_title')->nullable()->after('canonical_url');
            $table->text('og_description')->nullable()->after('og_title');
            $table->string('og_image')->nullable()->after('og_description');
            $table->text('schema_markup')->nullable()->after('og_image');
            $table->boolean('status')->default(true)->after('schema_markup');

            $table->index('slug');
            $table->index('category_id');
            $table->index('featured');
            $table->index('status');
            $table->index('price');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropIndex(['slug']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['featured']);
            $table->dropIndex(['status']);
            $table->dropIndex(['price']);
            $table->dropIndex(['created_at']);

            $table->dropColumn([
                'category_id',
                'slug',
                'sku',
                'short_description',
                'sale_price',
                'featured',
                'seo_title',
                'seo_description',
                'seo_keywords',
                'canonical_url',
                'og_title',
                'og_description',
                'og_image',
                'schema_markup',
                'status',
            ]);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->integer('discount_percentage')->default(0);
            $table->string('image_path')->nullable();
            $table->string('college');
            $table->string('category')->nullable();
            $table->decimal('rating', 2, 1)->default(4.8);
            $table->integer('reviews_count')->default(120);
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default('in_stock');
        });
    }
};
