<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermisoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('permiso')->insert([
            ['id' => 1, 'nombre' => 'USU_CREATE',  'etiqueta' => 'Crear Usuario',        'modulo' => 'Seguridad'],
            ['id' => 2, 'nombre' => 'DIAG_WRITE',  'etiqueta' => 'Registrar Diagnóstico','modulo' => 'Taller'],
            ['id' => 3, 'nombre' => 'PROF_EDIT',   'etiqueta' => 'Editar Proforma',      'modulo' => 'Ventas'],
        ]);

        // Asignar un permiso base a cada rol
        DB::table('rol_permiso')->insert([
            ['id_permiso' => 1, 'id_rol' => 1, 'estado' => 'Activo', 'fecha_registro' => now()->toDateString(), 'observaciones' => null],
            ['id_permiso' => 2, 'id_rol' => 2, 'estado' => 'Activo', 'fecha_registro' => now()->toDateString(), 'observaciones' => null],
            ['id_permiso' => 3, 'id_rol' => 3, 'estado' => 'Activo', 'fecha_registro' => now()->toDateString(), 'observaciones' => null],
        ]);
    }
}
