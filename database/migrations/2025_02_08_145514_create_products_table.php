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
            $table->foreignId('store_id')->constrained();
            $table->string('code');
            $table->string('name');
            $table->string('slug')->nullable()->unique();
            $table->text('description')->nullable();
            $table->string('short_description', 500)->nullable();
            $table->string('barcode', 100)->nullable();
            $table->string('barcode_image')->nullable();
            $table->foreignId('category_id')->constrained();
            $table->foreignId('tax_id')->nullable()->constrained();
            $table->foreignId('discount_id')->nullable()->constrained();
            $table->foreignId('supplier_id')->nullable()->constrained();
            $table->boolean('featured')->default(false);
            $table->boolean('is_active')->default(true);

            // SEO fields
            $table->string('seo_title', 60)->nullable();
            $table->string('seo_description', 160)->nullable();
            $table->string('seo_keywords')->nullable();
            $table->string('seo_canonical_url')->nullable();

            // OpenGraph specific
            $table->string('og_title', 95)->nullable();
            $table->string('og_description', 200)->nullable();
            $table->string('og_type', 50)->default('product');

            // Schema.org / JSON-LD fields
            $table->string('schema_brand')->nullable();
            $table->string('schema_sku')->nullable();
            $table->string('schema_gtin')->nullable();
            $table->string('schema_mpn')->nullable();

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
