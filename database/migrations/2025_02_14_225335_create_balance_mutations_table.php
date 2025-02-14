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
        Schema::create('balance_mutations', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['in', 'out']);
            $table->decimal('amount', 15, 2);
            $table->string('source_type');
            $table->bigInteger('source_id');
            $table->decimal('previous_balance', 15, 2);
            $table->decimal('current_balance', 15, 2);
            $table->text('notes');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_mutations');
    }
};
