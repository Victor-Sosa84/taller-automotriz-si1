<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioPersonalSeeder extends Seeder
{
    public function run(): void
    {
        // id_rol: 2 = Mecánico, 3 = Recepcionista (ver RolPersonalSeeder)
        $usuarios = [
            // Mecánicos
            [
                'id_rol'         => 2,
                'ci_personal'    => '4521837',
                'nombre_usuario' => 'lfernando',
                'clave'          => Hash::make('Taller2026!'),
                'correo'         => 'luis.mamani1@gmail.com',
            ],
            [
                'id_rol'         => 2,
                'ci_personal'    => '6739154',
                'nombre_usuario' => 'hariel',
                'clave'          => Hash::make('Taller2026!'),
                'correo'         => 'hugo.vaca2@gmail.com',
            ],
            [
                'id_rol'         => 2,
                'ci_personal'    => '8214673',
                'nombre_usuario' => 'mantonio',
                'clave'          => Hash::make('Taller2026!'),
                'correo'         => 'marco.salvatierra3@gmail.com',
            ],
            [
                'id_rol'         => 2,
                'ci_personal'    => '5390218',
                'nombre_usuario' => 'edaniel',
                'clave'          => Hash::make('Taller2026!'),
                'correo'         => 'esteban.choque4@gmail.com',
            ],
            // Recepcionistas
            [
                'id_rol'         => 3,
                'ci_personal'    => '7461923',
                'nombre_usuario' => 'dandrea',
                'clave'          => Hash::make('Taller2026!'),
                'correo'         => 'daniela.roca5@gmail.com',
            ],
            [
                'id_rol'         => 3,
                'ci_personal'    => '3957284',
                'nombre_usuario' => 'plucia',
                'clave'          => Hash::make('Taller2026!'),
                'correo'         => 'patricia.vargas6@gmail.com',
            ],
            [
                'id_rol'         => 3,
                'ci_personal'    => '6082417',
                'nombre_usuario' => 'gfernanda',
                'clave'          => Hash::make('Taller2026!'),
                'correo'         => 'gabriela.anez7@gmail.com',
            ],
        ];

        DB::table('usuario')->insertOrIgnore($usuarios);
    }
}
