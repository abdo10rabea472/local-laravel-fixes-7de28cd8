<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('users', 'customer_group_id')) {
            Schema::table('users', function (Blueprint $table) {
                try { $table->dropForeign(['customer_group_id']); } catch (\Throwable $e) {}
                $table->dropColumn('customer_group_id');
            });
        }
        Schema::dropIfExists('customer_groups');
    }

    public function down(): void
    {
        Schema::create('customer_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('badge_color', 20)->default('violet');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('customer_group_id')->nullable()->after('phone')->constrained('customer_groups')->nullOnDelete();
        });
    }
};
