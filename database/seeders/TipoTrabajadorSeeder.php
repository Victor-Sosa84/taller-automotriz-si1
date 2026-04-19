<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoTrabajadorSeeder extends Seeder
{
    public function run(): void
    {
        // Estos son los tipos de puesto/cargo que ocupa el personal del taller.
        // No confundir con 'rol' (acceso al sistema) — esto es el cargo laboral.
        // NO eliminar estos registros si ya hay personal asociado en 'adquiere'.
        DB::table('tipo_trabajador')->insert([
            ['id' => 1, 'descripcion' => 'Mecánico General'],
            ['id' => 2, 'descripcion' => 'Electricista Automotriz'],
            ['id' => 3, 'descripcion' => 'Asesor de Servicio'],
            ['id' => 4, 'descripcion' => 'Jefe de Taller'],
            ['id' => 5, 'descripcion' => 'Pintor y Latonero'],
            ['id' => 6, 'descripcion' => 'Técnico en Aire Acondicionado'],
        ]);
    }
}
