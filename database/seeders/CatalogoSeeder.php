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
            ['id' => 1, 'nombre' => 'Filtro de aceite',      'estado' => 'Disponible', 'marca' => 'Bosch',  'precio_referencial' => 35.00],
            ['id' => 2, 'nombre' => 'Pastillas de freno',    'estado' => 'Disponible', 'marca' => 'Brembo', 'precio_referencial' => 120.00],
            ['id' => 3, 'nombre' => 'Bujías',                'estado' => 'Disponible', 'marca' => 'NGK',    'precio_referencial' => 25.00],
            ['id' => 4, 'nombre' => 'Correa de distribución','estado' => 'Disponible', 'marca' => 'Gates',  'precio_referencial' => 85.00],
        ]);

        DB::table('mano_obra')->insertOrIgnore([
            ['id' => 1, 'descripcion' => 'Cambio de aceite',       'costo_referencial' => 50.00],
            ['id' => 2, 'descripcion' => 'Revisión de frenos',     'costo_referencial' => 80.00],
            ['id' => 3, 'descripcion' => 'Alineación y balanceo',  'costo_referencial' => 120.00],
            ['id' => 4, 'descripcion' => 'Diagnóstico eléctrico',  'costo_referencial' => 100.00],
        ]);
    }
}