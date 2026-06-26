<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('header_menu_items', function (Blueprint $table) {
            $table->string('type')->default('link')->after('url');
            $table->string('coupon_code')->nullable()->after('type');
            $table->unsignedTinyInteger('coupon_percent')->nullable()->after('coupon_code');
        });
    }

    public function down(): void
    {
        Schema::table('header_menu_items', function (Blueprint $table) {
            $table->dropColumn(['type', 'coupon_code', 'coupon_percent']);
        });
    }
};
