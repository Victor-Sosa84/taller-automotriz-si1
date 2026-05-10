<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Desactivamos la revisión de llaves foráneas
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Primero limpiamos datos existentes para evitar conflictos
        \DB::table('rol_permiso')->truncate();
        \DB::table('permiso')->truncate();

        // 3. Reactivamos la revisión (IMPORTANTE hacerlo antes de alterar la estructura)
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');

        Schema::table('permiso', function (Blueprint $table) {
            // Eliminar columna modulo y agregar las dos nuevas
            $table->dropColumn('modulo');
            $table->string('caso_uso', 10)->after('etiqueta');   // CU-01, CU-02, etc.
            $table->string('paquete', 100)->after('caso_uso');   // P1: Gestión de Recepción, etc.
        });
    }

    public function down(): void
    {
        Schema::table('permiso', function (Blueprint $table) {
            $table->dropColumn(['caso_uso', 'paquete']);
            $table->string('modulo', 50)->nullable();
        });
    }
};