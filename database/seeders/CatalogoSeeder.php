<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogoSeeder extends Seeder
{
    public function run(): void
    {
        // database/seeders/CatalogoSeeder.php
        DB::table('repuesto')->insertOrIgnore([
            ['id' => 1, 'nombre' => 'Filtro de aceite',    'estado' => 'Disponible', 'marca' => 'Bosch'],
            ['id' => 2, 'nombre' => 'Pastillas de freno',  'estado' => 'Disponible', 'marca' => 'Brembo'],
            ['id' => 3, 'nombre' => 'Bujías',              'estado' => 'Disponible', 'marca' => 'NGK'],
            ['id' => 4, 'nombre' => 'Correa de distribución','estado' => 'Disponible', 'marca' => 'Gates'],
        ]);

        DB::table('mano_obra')->insertOrIgnore([
            ['id' => 1, 'descripcion' => 'Cambio de aceite'],
            ['id' => 2, 'descripcion' => 'Revisión de frenos'],
            ['id' => 3, 'descripcion' => 'Alineación y balanceo'],
            ['id' => 4, 'descripcion' => 'Diagnóstico eléctrico'],
        ]);
    }
}