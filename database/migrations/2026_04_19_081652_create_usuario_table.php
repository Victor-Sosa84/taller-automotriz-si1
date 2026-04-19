<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->integer('id_usuario')->autoIncrement()->primary();
            $table->integer('id_rol');
            $table->string('ci_personal', 20);
            $table->string('nombre_usuario', 50);
            $table->string('clave', 255);
            $table->string('correo', 100)->nullable();

            $table->foreign('id_rol')
                  ->references('id')->on('rol')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('ci_personal')
                  ->references('ci')->on('persona')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};