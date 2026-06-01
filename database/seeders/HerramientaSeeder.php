<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HerramientaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('herramienta')->insertOrIgnore([
            ['nro' => 1, 'id_tipo_herramienta' => 1, 'id_marca_herramienta' => 1, 'descripcion' => 'Juego de llaves combinadas', 'estado' => 'Bueno', 'disponible' => true],
            ['nro' => 2, 'id_tipo_herramienta' => 1, 'id_marca_herramienta' => 4, 'descripcion' => 'Juego de destornilladores', 'estado' => 'Bueno', 'disponible' => true],
            ['nro' => 3, 'id_tipo_herramienta' => 2, 'id_marca_herramienta' => 2, 'descripcion' => 'Taladro percutor', 'estado' => 'Bueno', 'disponible' => true],
            ['nro' => 4, 'id_tipo_herramienta' => 4, 'id_marca_herramienta' => 6, 'descripcion' => 'Gato hidráulico 3T', 'estado' => 'Bueno', 'disponible' => true],
            ['nro' => 5, 'id_tipo_herramienta' => 5, 'id_marca_herramienta' => 3, 'descripcion' => 'Micrómetro digital', 'estado' => 'Bueno', 'disponible' => true],
            ['nro' => 6, 'id_tipo_herramienta' => 6, 'id_marca_herramienta' => 2, 'descripcion' => 'Scanner OBD2', 'estado' => 'Bueno', 'disponible' => true],
        ]);
    }
}