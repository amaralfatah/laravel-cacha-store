<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_unit_id')->constrained('product_units');
            $table->enum('type', ['in', 'out', 'adjustment']);
            $table->decimal('quantity', 15, 2);
            $table->text('notes');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
