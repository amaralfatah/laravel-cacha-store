<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained();
            $table->foreignId('supplier_id')->constrained();
            $table->string('invoice_number')->unique();
            $table->decimal('total_amount', 15, 2);
            $table->foreignId('tax_id')->nullable()->constrained();
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->foreignId('discount_id')->nullable()->constrained();
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('final_amount', 15, 2);
            $table->enum('payment_type', ['cash', 'transfer']);
            $table->string('reference_number')->nullable();
            $table->enum('status', ['draft', 'pending', 'completed', 'cancelled']);
            $table->timestamp('purchase_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained();
            $table->foreignId('unit_id')->constrained();
            $table->decimal('quantity', 15, 2);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->decimal('discount', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
    }
};
