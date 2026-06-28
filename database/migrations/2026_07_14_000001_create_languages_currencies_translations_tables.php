<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->string('native_name', 80);
            $table->string('code', 10)->unique();
            $table->string('locale', 20);
            $table->enum('direction', ['ltr', 'rtl'])->default('ltr');
            $table->string('flag')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->string('code', 10)->unique();
            $table->string('symbol', 10);
            $table->enum('symbol_position', ['before', 'after'])->default('before');
            $table->unsignedTinyInteger('decimals')->default(2);
            $table->string('decimal_separator', 4)->default('.');
            $table->string('thousands_separator', 4)->default(',');
            $table->decimal('exchange_rate', 18, 8)->default(1);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 20)->index();
            $table->string('group', 80)->index();
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();
            $table->unique(['locale', 'group', 'key'], 'translations_locale_group_key_unique');
        });

        // Seed defaults
        $now = now();
        DB::table('languages')->insert([
            ['name' => 'English', 'native_name' => 'English', 'code' => 'en', 'locale' => 'en', 'direction' => 'ltr', 'is_default' => true,  'is_active' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Arabic',  'native_name' => 'العربية', 'code' => 'ar', 'locale' => 'ar', 'direction' => 'rtl', 'is_default' => false, 'is_active' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('currencies')->insert([
            ['name' => 'US Dollar',     'code' => 'USD', 'symbol' => '$',   'symbol_position' => 'before', 'decimals' => 2, 'decimal_separator' => '.', 'thousands_separator' => ',', 'exchange_rate' => 1,        'is_default' => true,  'is_active' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Egyptian Pound','code' => 'EGP', 'symbol' => 'ج.م', 'symbol_position' => 'after',  'decimals' => 2, 'decimal_separator' => '.', 'thousands_separator' => ',', 'exchange_rate' => 48.5,     'is_default' => false, 'is_active' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Euro',          'code' => 'EUR', 'symbol' => '€',   'symbol_position' => 'before', 'decimals' => 2, 'decimal_separator' => '.', 'thousands_separator' => ',', 'exchange_rate' => 0.92,    'is_default' => false, 'is_active' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('languages');
    }
};
