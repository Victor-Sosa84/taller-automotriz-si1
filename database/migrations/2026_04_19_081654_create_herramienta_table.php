<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('herramienta', function (Blueprint $table) {
            // PK se llama 'nro', no 'id'
            $table->integer('nro')->autoIncrement()->primary();
            $table->integer('id_tipo_herramienta');
            $table->integer('id_marca_herramienta');
            $table->string('descripcion', 150)->nullable();
            $table->string('estado', 50)->nullable();
            $table->boolean('disponible')->default(true);

            $table->foreign('id_tipo_herramienta')
                  ->references('id')->on('tipo_herramienta')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('id_marca_herramienta')
                  ->references('id')->on('marca_herramienta')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('herramienta');
    }
};