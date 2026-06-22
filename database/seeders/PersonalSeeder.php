<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonalSeeder extends Seeder
{
    public function run(): void
    {
        // ── Personal (tabla persona, es_personal=1) ───────────────────
        DB::table('persona')->insertOrIgnore([
            // Mecánicos
            [
                'ci'          => '4521837',
                'nombre'      => 'Luis Fernando Mamani Quispe',
                'telefono'    => '70215843',
                'direccion'   => 'Av. Mutualista, Barrio Sirari, Santa Cruz',
                'nit'         => null,
                'es_cliente'  => 0,
                'es_personal' => 1,
            ],
            [
                'ci'          => '6739154',
                'nombre'      => 'Hugo Ariel Vaca Justiniano',
                'telefono'    => '71548930',
                'direccion'   => 'Av. Beni, 4to Anillo, Santa Cruz',
                'nit'         => null,
                'es_cliente'  => 0,
                'es_personal' => 1,
            ],
            [
                'ci'          => '8214673',
                'nombre'      => 'Marco Antonio Salvatierra Rivero',
                'telefono'    => '72193845',
                'direccion'   => 'Barrio Sevilla, Av. Paraguá, Santa Cruz',
                'nit'         => null,
                'es_cliente'  => 0,
                'es_personal' => 1,
            ],
            [
                'ci'          => '5390218',
                'nombre'      => 'Esteban Daniel Choque Aramayo',
                'telefono'    => '73824916',
                'direccion'   => 'Plan 3000, UV 45, Santa Cruz',
                'nit'         => null,
                'es_cliente'  => 0,
                'es_personal' => 1,
            ],
            // Recepcionistas
            [
                'ci'          => '7461923',
                'nombre'      => 'Daniela Andrea Roca Suárez',
                'telefono'    => '76918243',
                'direccion'   => 'Av. Alemana, 3er Anillo, Santa Cruz',
                'nit'         => null,
                'es_cliente'  => 0,
                'es_personal' => 1,
            ],
            [
                'ci'          => '3957284',
                'nombre'      => 'Patricia Lucía Vargas Méndez',
                'telefono'    => '77624810',
                'direccion'   => 'Urb. Las Palmas, Av. Cristo Redentor, Santa Cruz',
                'nit'         => null,
                'es_cliente'  => 0,
                'es_personal' => 1,
            ],
            [
                'ci'          => '6082417',
                'nombre'      => 'Gabriela Fernanda Áñez Soliz',
                'telefono'    => '78316492',
                'direccion'   => 'Av. Banzer, entre 2do y 3er Anillo, Santa Cruz',
                'nit'         => null,
                'es_cliente'  => 0,
                'es_personal' => 1,
            ],
        ]);

        // ── Tipo de trabajador (adquiere) ─────────────────────────────
        // Lookup dinámico por descripción para no asumir IDs fijos.
        $idMecanicoGeneral = DB::table('tipo_trabajador')->where('descripcion', 'Mecánico General')->value('id');
        $idAsesorServicio  = DB::table('tipo_trabajador')->where('descripcion', 'Asesor de Servicio')->value('id');

        $adquiere = [];

        $mecanicos      = ['4521837', '6739154', '8214673', '5390218'];
        $recepcionistas = ['7461923', '3957284', '6082417'];

        if ($idMecanicoGeneral) {
            foreach ($mecanicos as $ci) {
                $adquiere[] = ['ci_personal' => $ci, 'id_tipo_trabajador' => $idMecanicoGeneral];
            }
        }

        if ($idAsesorServicio) {
            foreach ($recepcionistas as $ci) {
                $adquiere[] = ['ci_personal' => $ci, 'id_tipo_trabajador' => $idAsesorServicio];
            }
        }

        if (!empty($adquiere)) {
            DB::table('adquiere')->insertOrIgnore($adquiere);
        }
    }
}
