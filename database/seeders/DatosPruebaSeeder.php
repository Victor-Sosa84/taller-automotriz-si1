<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatosPruebaSeeder extends Seeder
{
    public function run(): void
    {
        // ── Clientes (tabla persona) ──────────────────────────────────
        DB::table('persona')->insertOrIgnore([
            [
                'ci'          => '7234891',
                'nombre'      => 'Carlos Alberto Romero Vaca',
                'telefono'    => '76234891',
                'direccion'   => 'Av. Cristo Redentor, Barrio Las Palmas, Santa Cruz',
                'nit'         => null,
                'es_cliente'  => 1,
                'es_personal' => 0,
            ],
            [
                'ci'          => '5812374',
                'nombre'      => 'María Alejandra Suárez Peña',
                'telefono'    => '77812374',
                'direccion'   => 'Equipetrol Norte, Calle 3 Oeste, Santa Cruz',
                'nit'         => null,
                'es_cliente'  => 1,
                'es_personal' => 0,
            ],
            [
                'ci'          => '8143650',
                'nombre'      => 'Juan Pablo Gutiérrez Flores',
                'telefono'    => '78143650',
                'direccion'   => 'Av. Banzer km 5, Urb. Los Jardines, Santa Cruz',
                'nit'         => null,
                'es_cliente'  => 1,
                'es_personal' => 0,
            ],
            [
                'ci'          => '6957423',
                'nombre'      => 'Rosa Elena Medina Chávez',
                'telefono'    => '75957423',
                'direccion'   => 'Plan 3000, Calle 12, Mz. 14, Santa Cruz',
                'nit'         => null,
                'es_cliente'  => 1,
                'es_personal' => 0,
            ],
            [
                'ci'          => '9341287',
                'nombre'      => 'Fernando Javier Ortiz Mamani',
                'telefono'    => '79341287',
                'direccion'   => 'Av. Roca y Coronado, Zona Norte, Santa Cruz',
                'nit'         => null,
                'es_cliente'  => 1,
                'es_personal' => 0,
            ],
        ]);

        // ── Autos (tabla auto) ────────────────────────────────────────
        // Sin FK hacia persona — el vínculo ocurre luego vía proforma.
        DB::table('auto')->insertOrIgnore([
            [
                'placa' => '3847GKT',
                'marca' => 'Toyota',
                'modelo' => 'Hilux',
                'anio'  => 2019,
                'color' => 'Blanco',
                'tipo'  => 'Camioneta',
            ],
            [
                'placa' => '1562MRZ',
                'marca' => 'Suzuki',
                'modelo' => 'Grand Vitara',
                'anio'  => 2015,
                'color' => 'Gris',
                'tipo'  => 'SUV',
            ],
            [
                'placa' => '7091BNF',
                'marca' => 'Nissan',
                'modelo' => 'Frontier',
                'anio'  => 2021,
                'color' => 'Negro',
                'tipo'  => 'Pickup',
            ],
            [
                'placa' => '4430CVL',
                'marca' => 'Mitsubishi',
                'modelo' => 'L200',
                'anio'  => 2017,
                'color' => 'Rojo',
                'tipo'  => 'Furgoneta',
            ],
            [
                'placa' => '8275JPW',
                'marca' => 'Toyota',
                'modelo' => 'RAV4',
                'anio'  => 2023,
                'color' => 'Azul',
                'tipo'  => 'Hatchback',
            ],
        ]);
    }
}
