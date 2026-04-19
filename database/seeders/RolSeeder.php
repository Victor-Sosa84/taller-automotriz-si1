<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('rol')->insert([
            ['id' => 1, 'nombre' => 'Administrador',  'descripcion' => 'Control total del sistema'],
            ['id' => 2, 'nombre' => 'Mecanico Jefe',  'descripcion' => 'Asigna tareas y cierra órdenes'],
            ['id' => 3, 'nombre' => 'Recepcionista',  'descripcion' => 'Gestiona ingresos y clientes'],
        ]);
    }
}
