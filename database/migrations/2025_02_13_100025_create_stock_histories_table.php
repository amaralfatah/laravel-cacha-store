<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_unit_id')->constrained('product_units');
            $table->string('reference_type'); // stock_adjustments, transactions, stock_takes
            $table->unsignedBigInteger('reference_id');
            $table->enum('type', ['in', 'out', 'adjustment']);
            $table->decimal('quantity', 15, 2);
            $table->decimal('remaining_stock', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_histories');
    }
};
