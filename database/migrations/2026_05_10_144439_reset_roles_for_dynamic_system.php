<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Limpiar roles existentes para re-seedear con nueva estructura
        // (los FK en usuario ya tienen ON DELETE CASCADE en rol)
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');
        \DB::table('rol_permiso')->truncate();
        \DB::table('rol')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // La tabla rol ya tiene la estructura correcta (id, nombre, descripcion)
        // No necesita cambios estructurales — solo re-seedear con un solo rol base
    }

    public function down(): void
    {
        // No hay cambio estructural que revertir
    }
};