<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('import_logs', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('status'); // pending, processing, completed, failed
            $table->text('error')->nullable();
            $table->integer('total_rows')->default(0);
            $table->integer('processed_rows')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('import_logs');
    }
};
