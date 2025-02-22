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
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('cash_amount', 15, 2)->nullable()->after('final_amount')
                ->comment('Jumlah uang tunai yang diberikan customer');
            $table->decimal('change_amount', 15, 2)->nullable()->after('cash_amount')
                ->comment('Jumlah kembalian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['cash_amount', 'change_amount']);
        });
    }
};
