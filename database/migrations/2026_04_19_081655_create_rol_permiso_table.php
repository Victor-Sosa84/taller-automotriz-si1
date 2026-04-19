<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rol_permiso', function (Blueprint $table) {
            // PK compuesta: id_permiso + id_rol
            $table->integer('id_permiso');
            $table->integer('id_rol');
            $table->string('estado', 20)->nullable();
            $table->date('fecha_registro')->nullable();
            $table->text('observaciones')->nullable();

            $table->primary(['id_permiso', 'id_rol']);

            $table->foreign('id_permiso')
                  ->references('id')->on('permiso')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('id_rol')
                  ->references('id')->on('rol')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rol_permiso');
    }
};