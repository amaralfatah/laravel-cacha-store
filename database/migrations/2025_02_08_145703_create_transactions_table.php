<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('store_id')->nullable()->index();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('cashier_id')->constrained('users');
            $table->decimal('total_amount', 15, 2);
            $table->foreignId('tax_id')->nullable()->constrained();
            $table->decimal('tax_amount', 15, 2);
            $table->foreignId('discount_id')->nullable()->constrained();
            $table->decimal('discount_amount', 15, 2);
            $table->decimal('final_amount', 15, 2);
            $table->enum('payment_type', ['cash', 'transfer']);
            $table->string('reference_number')->nullable();
            $table->enum('status', ['pending', 'success', 'failed', 'cancelled', 'returned']);
            $table->timestamp('invoice_date');




            $table->timestamp('returned_at')->nullable();
            $table->unsignedBigInteger('returned_by')->nullable();
            $table->string('return_reason')->nullable();
            $table->text('return_notes')->nullable();

            $table->foreign('returned_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
