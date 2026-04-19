<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factura', function (Blueprint $table) {
            $table->integer('nro')->autoIncrement()->primary();
            $table->dateTime('fecha_emision');
            $table->string('nit', 20)->nullable();
            $table->string('nombre', 100)->nullable();
            $table->decimal('total', 10, 2);
            $table->date('plazo')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factura');
    }
};