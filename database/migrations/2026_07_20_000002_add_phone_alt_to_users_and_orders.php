<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'phone_alt')) {
                $table->string('phone_alt', 30)->nullable()->after('phone');
            }
        });
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'phone_alt')) {
                $table->string('phone_alt', 30)->nullable()->after('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'phone_alt')) $table->dropColumn('phone_alt');
        });
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'phone_alt')) $table->dropColumn('phone_alt');
        });
    }
};
