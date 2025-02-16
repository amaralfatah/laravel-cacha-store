<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 255);
            $table->text('address');
            $table->string('phone', 20);
            $table->string('email', 255);
            $table->string('logo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
