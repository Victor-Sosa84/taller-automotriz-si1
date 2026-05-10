<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        // Un solo rol base — el resto se crean desde la interfaz
        // El usuario id=1 (Admin Principal) tiene acceso total sin consultar rol_permiso
        DB::table('rol')->insertOrIgnore([
            [
                'id'          => 1,
                'nombre'      => 'Administrador del Sistema',
                'descripcion' => 'Acceso total al sistema. Gestiona usuarios, roles y privilegios.',
            ],
        ]);
    }
}