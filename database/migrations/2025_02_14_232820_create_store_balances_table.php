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
        Schema::create('store_balances', function (Blueprint $table) {
            $table->id();
            $table->decimal('cash_amount', 15, 2)->default(0);
            $table->decimal('non_cash_amount', 15, 2)->default(0);
            $table->foreignId('last_updated_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_balances');
    }
};
