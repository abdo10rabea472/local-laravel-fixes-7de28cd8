<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('orders') || ! Schema::hasColumn('orders', 'payment_status')) {
            return;
        }

        // Expand enum to include all payment lifecycle states used by the app.
        DB::statement("ALTER TABLE `orders` MODIFY `payment_status` ENUM(
            'unpaid','pending','processing','paid','cod_pending','failed','cancelled','refunded'
        ) NOT NULL DEFAULT 'unpaid'");
    }

    public function down(): void
    {
        if (! Schema::hasTable('orders') || ! Schema::hasColumn('orders', 'payment_status')) {
            return;
        }

        DB::statement("UPDATE `orders` SET `payment_status` = 'unpaid'
            WHERE `payment_status` NOT IN ('unpaid','paid','refunded','failed')");

        DB::statement("ALTER TABLE `orders` MODIFY `payment_status` ENUM(
            'unpaid','paid','refunded','failed'
        ) NOT NULL DEFAULT 'unpaid'");
    }
};
