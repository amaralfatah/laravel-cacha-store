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
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id(); // Kolom id dengan tipe BIGINT, auto increment
            $table->foreignId('product_unit_id')->constrained('product_units'); // Kolom product_unit_id dengan referensi ke tabel product_units
            $table->enum('type', ['in', 'out', 'adjustment']); // Kolom type sebagai enum
            $table->decimal('quantity', 15, 2); // Kolom quantity dengan tipe decimal
            $table->decimal('remaining_stock', 15, 2); // Kolom remaining_stock dengan tipe decimal
            $table->text('notes')->nullable(); // Kolom notes yang bisa bernilai null
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_histories');
    }
};
