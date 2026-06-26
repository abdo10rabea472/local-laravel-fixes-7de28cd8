<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Stock movements — full audit trail of every quantity change
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('quantity_change'); // +ve = restock, -ve = consumption
            $table->unsignedInteger('stock_before');
            $table->unsignedInteger('stock_after');
            $table->enum('type', ['manual', 'order', 'order_cancel', 'return', 'adjustment', 'bulk_update'])->index();
            $table->string('reference_type', 50)->nullable(); // Order, ReturnRequest...
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('note')->nullable();
            $table->string('changed_by_type', 20)->nullable(); // admin|system
            $table->unsignedBigInteger('changed_by_id')->nullable();
            $table->timestamps();
            $table->index(['product_id', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
        });

        // Add low-stock threshold to products
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'low_stock_threshold')) {
                $table->unsignedInteger('low_stock_threshold')->default(5)->after('stock');
            }
        });

        // Shipping carriers (DHL, Aramex, Bosta...)
        Schema::create('shipping_carriers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 50)->unique();
            $table->string('tracking_url_template')->nullable(); // e.g. https://x.com/track/{tracking}
            $table->string('contact_phone', 30)->nullable();
            $table->string('contact_email')->nullable();
            $table->decimal('default_cost', 10, 2)->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Add carrier link + actual cost to orders (keep legacy string columns)
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'shipping_carrier_id')) {
                $table->foreignId('shipping_carrier_id')->nullable()->after('shipping_carrier')
                    ->constrained('shipping_carriers')->nullOnDelete();
            }
            if (!Schema::hasColumn('orders', 'actual_shipping_cost')) {
                $table->decimal('actual_shipping_cost', 12, 2)->nullable()->after('shipping_cost');
            }
            if (!Schema::hasColumn('orders', 'shipped_notes')) {
                $table->text('shipped_notes')->nullable()->after('actual_shipping_cost');
            }
        });

        // Return requests (RMA)
        Schema::create('return_requests', function (Blueprint $table) {
            $table->id();
            $table->string('rma_number', 30)->unique();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', [
                'pending','approved','rejected','received','refunded','cancelled'
            ])->default('pending')->index();
            $table->enum('reason', [
                'defective','wrong_item','not_as_described','damaged','no_longer_wanted','other'
            ])->default('other');
            $table->text('customer_note')->nullable();
            $table->text('admin_note')->nullable();
            $table->decimal('refund_amount', 12, 2)->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
            $table->index(['order_id']);
            $table->index(['user_id']);
        });

        Schema::create('return_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_request_id')->constrained('return_requests')->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('line_total', 12, 2);
            $table->boolean('restock')->default(true);
            $table->timestamps();
            $table->index(['return_request_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_request_items');
        Schema::dropIfExists('return_requests');
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'shipping_carrier_id')) {
                $table->dropForeign(['shipping_carrier_id']);
                $table->dropColumn('shipping_carrier_id');
            }
            if (Schema::hasColumn('orders', 'actual_shipping_cost')) $table->dropColumn('actual_shipping_cost');
            if (Schema::hasColumn('orders', 'shipped_notes')) $table->dropColumn('shipped_notes');
        });
        Schema::dropIfExists('shipping_carriers');
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'low_stock_threshold')) $table->dropColumn('low_stock_threshold');
        });
        Schema::dropIfExists('stock_movements');
    }
};
