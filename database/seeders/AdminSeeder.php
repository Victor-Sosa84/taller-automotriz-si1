<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Un solo usuario base — el Admin Principal (id=1)
        // Los demás usuarios se crean desde la interfaz
        DB::table('persona')->insertOrIgnore([
            'ci'          => '00000001',
            'nombre'      => 'Administrador Principal',
            'telefono'    => null,
            'direccion'   => null,
            'es_cliente'  => false,
            'es_personal' => true,
        ]);

        DB::table('usuario')->insertOrIgnore([
            'id_usuario'     => 1,
            'id_rol'         => 1,
            'ci_personal'    => '00000001',
            'nombre_usuario' => 'aprincipal',
            'clave'          => Hash::make(env('ADMIN_PASSWORD') ?: 'Admin1234!'),
            'correo'         => 'admin@taller.com',
        ]);
    }
}