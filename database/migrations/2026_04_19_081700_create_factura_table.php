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
            $table->integer('nro_orden_trabajo');
            $table->dateTime('fecha_emision');
            $table->string('nit', 20);
            $table->string('nombre', 100);
            $table->decimal('total', 10, 2);
            $table->date('plazo')->nullable();

            $table->foreign('nro_orden_trabajo')
                ->references('nro')->on('orden_trabajo')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factura');
    }
};