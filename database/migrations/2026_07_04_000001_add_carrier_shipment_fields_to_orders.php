<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'shipment_number')) {
                $table->string('shipment_number', 100)->nullable()->after('tracking_number');
            }
            if (!Schema::hasColumn('orders', 'shipping_status')) {
                $table->string('shipping_status', 40)->nullable()->default('pending')->after('shipment_number');
            }
            if (!Schema::hasColumn('orders', 'label_url')) {
                $table->string('label_url', 500)->nullable()->after('shipping_status');
            }
            if (!Schema::hasColumn('orders', 'barcode')) {
                $table->string('barcode', 100)->nullable()->after('label_url');
            }
            if (!Schema::hasColumn('orders', 'tracking_url')) {
                $table->string('tracking_url', 500)->nullable()->after('barcode');
            }
            if (!Schema::hasColumn('orders', 'pickup_address')) {
                $table->json('pickup_address')->nullable()->after('tracking_url');
            }
            if (!Schema::hasColumn('orders', 'pickup_datetime')) {
                $table->dateTime('pickup_datetime')->nullable()->after('pickup_address');
            }
            if (!Schema::hasColumn('orders', 'carrier_response')) {
                $table->json('carrier_response')->nullable()->after('pickup_datetime');
            }
            if (!Schema::hasColumn('orders', 'shipping_error')) {
                $table->text('shipping_error')->nullable()->after('carrier_response');
            }
            if (!Schema::hasColumn('orders', 'shipping_attempts')) {
                $table->unsignedTinyInteger('shipping_attempts')->default(0)->after('shipping_error');
            }
            if (!Schema::hasColumn('orders', 'shipment_created_at')) {
                $table->dateTime('shipment_created_at')->nullable()->after('shipping_attempts');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            $indexes = collect(Schema::getIndexes('orders'))->pluck('name')->all();
            if (!in_array('orders_shipping_status_index', $indexes, true)) {
                $table->index('shipping_status');
            }
            if (!in_array('orders_shipment_number_index', $indexes, true)) {
                $table->index('shipment_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            foreach ([
                'shipment_number','shipping_status','label_url','barcode','tracking_url',
                'pickup_address','pickup_datetime','carrier_response','shipping_error',
                'shipping_attempts','shipment_created_at',
            ] as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
