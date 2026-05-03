<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bitacora', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('id_usuario');
            $table->dateTime('fecha_hora')->useCurrent();
            $table->string('accion', 255);
            $table->string('ip_equipo', 45)->nullable();

            $table->foreign('id_usuario')
                  ->references('id_usuario')->on('usuario')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacora');
    }
};