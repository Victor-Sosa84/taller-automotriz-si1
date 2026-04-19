<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestamo_herramienta', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->dateTime('fecha_salida');
            $table->dateTime('fecha_devolucion')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestamo_herramienta');
    }
};