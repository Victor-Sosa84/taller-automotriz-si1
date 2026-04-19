<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auto', function (Blueprint $table) {
            // PK es la placa — string, no autoincrement
            $table->string('placa', 10)->primary();
            $table->string('marca', 50)->nullable();
            $table->string('modelo', 50)->nullable();
            $table->integer('anio')->nullable();
            $table->string('color', 30)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto');
    }
};