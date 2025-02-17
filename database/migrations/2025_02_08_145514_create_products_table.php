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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('barcode', 100);
            $table->string('barcode_image')->nullable();
            $table->foreignId('store_id')->nullable()->index();
            $table->foreignId('category_id')->nullable()->constrained();
            $table->foreignId('tax_id')->nullable()->constrained();
            $table->foreignId('discount_id')->nullable()->constrained();
            $table->foreignId('supplier_id')->nullable()->constrained('units');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
