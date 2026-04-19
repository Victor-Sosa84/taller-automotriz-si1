<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_prestamo', function (Blueprint $table) {
            $table->integer('id_prestamo_herramienta');
            $table->integer('nro_herramienta');
            $table->string('estado_salida', 50)->nullable();
            $table->string('estado_retorno', 50)->nullable();

            $table->primary(['id_prestamo_herramienta', 'nro_herramienta']);

            $table->foreign('id_prestamo_herramienta')
                  ->references('id')->on('prestamo_herramienta')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('nro_herramienta')
                  ->references('nro')->on('herramienta')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_prestamo');
    }
};