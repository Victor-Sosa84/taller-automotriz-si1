<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarcaHerramientaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('marca_herramienta')->insert([
            ['id' => 1, 'nombre' => 'Stanley'],
            ['id' => 2, 'nombre' => 'Bosch'],
            ['id' => 3, 'nombre' => 'Snap-on'],
            ['id' => 4, 'nombre' => 'Truper'],
            ['id' => 5, 'nombre' => 'Dewalt'],
            ['id' => 6, 'nombre' => 'Wurth'],
        ]);
    }
}
