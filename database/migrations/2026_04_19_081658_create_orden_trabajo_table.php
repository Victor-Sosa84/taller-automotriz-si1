<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orden_trabajo', function (Blueprint $table) {
            $table->integer('nro')->autoIncrement()->primary();
            $table->integer('nro_proforma');
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin')->nullable();
            $table->string('estado', 50)->nullable();
            $table->integer('kilometraje')->nullable();
            $table->text('observacion_entrada')->nullable();
            $table->text('observacion_salida')->nullable();

            $table->foreign('nro_proforma')
                  ->references('nro')->on('proforma')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orden_trabajo');
    }
};