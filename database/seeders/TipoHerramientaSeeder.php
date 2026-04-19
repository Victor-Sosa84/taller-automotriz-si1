<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoHerramientaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tipo_herramienta')->insert([
            ['id' => 1, 'descripcion' => 'Manual'],
            ['id' => 2, 'descripcion' => 'Eléctrica'],
            ['id' => 3, 'descripcion' => 'Neumática'],
            ['id' => 4, 'descripcion' => 'Hidráulica'],
            ['id' => 5, 'descripcion' => 'Medición y Precisión'],
            ['id' => 6, 'descripcion' => 'Diagnóstico Scanner'],
        ]);
    }
}
