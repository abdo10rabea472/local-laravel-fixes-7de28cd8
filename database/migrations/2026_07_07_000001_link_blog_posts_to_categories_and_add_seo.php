<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the FK that pointed to blog_categories so we can reuse the column for product categories.
        Schema::table('blog_posts', function (Blueprint $table) {
            try { $table->dropForeign(['blog_category_id']); } catch (\Throwable $e) {}
            try { $table->dropIndex(['blog_category_id', 'published_at']); } catch (\Throwable $e) {}
        });

        // Reset any orphan references so we can attach the new FK safely.
        DB::statement('UPDATE blog_posts SET blog_category_id = NULL');

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->foreign('blog_category_id')
                ->references('id')->on('categories')
                ->nullOnDelete();
            $table->index(['blog_category_id', 'published_at']);

            // SEO fields
            $table->string('meta_title')->nullable()->after('excerpt');
            $table->string('meta_description', 320)->nullable()->after('meta_title');
            $table->string('meta_keywords')->nullable()->after('meta_description');
            $table->string('og_image')->nullable()->after('meta_keywords');
            $table->string('canonical_url')->nullable()->after('og_image');
            $table->boolean('no_index')->default(false)->after('canonical_url');
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            try { $table->dropForeign(['blog_category_id']); } catch (\Throwable $e) {}
            $table->dropColumn(['meta_title','meta_description','meta_keywords','og_image','canonical_url','no_index']);
            $table->foreign('blog_category_id')->references('id')->on('blog_categories')->nullOnDelete();
        });
    }
};
