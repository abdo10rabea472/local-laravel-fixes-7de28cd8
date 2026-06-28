<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Publish any older posts that were saved before auto-publish was added.
        DB::statement('UPDATE blog_posts SET published_at = COALESCE(created_at, NOW()) WHERE published_at IS NULL');
    }

    public function down(): void {}
};
