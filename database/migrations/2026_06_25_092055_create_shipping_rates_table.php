<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->string('country')->default('Egypt');
            $table->string('state');
            $table->string('city')->nullable();
            $table->decimal('cost', 10, 2)->default(0);
            $table->integer('position')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->index(['country', 'state', 'city']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_rates');
    }
};
