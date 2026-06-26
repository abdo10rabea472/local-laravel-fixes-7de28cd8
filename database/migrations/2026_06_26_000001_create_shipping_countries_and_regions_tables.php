<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_countries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('cost', 10, 2)->nullable();
            $table->integer('position')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('shipping_regions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('shipping_countries')->cascadeOnDelete();
            $table->string('name'); // محافظة / مدينة / ولاية
            $table->decimal('cost', 10, 2)->nullable();
            $table->integer('position')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->index(['country_id', 'name']);
        });

        // ترحيل البيانات القديمة إن وجدت
        if (Schema::hasTable('shipping_rates')) {
            $oldRates = DB::table('shipping_rates')->get();
            $countryIds = [];
            foreach ($oldRates as $rate) {
                $countryName = $rate->country ?: 'Egypt';
                if (! isset($countryIds[$countryName])) {
                    $countryIds[$countryName] = DB::table('shipping_countries')->insertGetId([
                        'name' => $countryName,
                        'cost' => null,
                        'position' => 0,
                        'status' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                $regionName = trim(($rate->state ?? '') . ($rate->city ? ' - ' . $rate->city : ''));
                if ($regionName === '') continue;
                DB::table('shipping_regions')->insert([
                    'country_id' => $countryIds[$countryName],
                    'name' => $regionName,
                    'cost' => $rate->cost,
                    'position' => $rate->position ?? 0,
                    'status' => (bool) ($rate->status ?? true),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            Schema::drop('shipping_rates');
        }

        // إضافة إعداد تفعيل الشحن المجاني
        if (! DB::table('site_settings')->where('key', 'free_shipping_enabled')->exists()) {
            DB::table('site_settings')->insert([
                'key' => 'free_shipping_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'shipping',
                'label' => 'تفعيل الشحن المجاني',
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_regions');
        Schema::dropIfExists('shipping_countries');
    }
};
