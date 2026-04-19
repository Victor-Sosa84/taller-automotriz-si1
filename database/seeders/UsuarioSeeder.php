<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        // ── Usuarios base para pruebas ────────────────────────────
        // IMPORTANTE: cambiar claves antes de subir a producción.
        $usuarios = [
            [
                'ci'     => '00000001',
                'nombre' => 'Administrador Principal',
                'correo' => 'admin@taller.com',
                // Si la variable en el .env está vacía o no existe, se usa la clave por defecto
                'clave' => env('ADMIN_PASSWORD') ?: 'Admin1234!',
                'id_rol' => 1,
            ],
            [
                'ci'     => '00000002',
                'nombre' => 'Mecanico Prueba',
                'correo' => 'mecanico@taller.com',
                // Si la variable en el .env está vacía o no existe, se usa la clave por defecto
                'clave'  => env('MECANICO_PASSWORD') ?: 'Mecanico1234!', 
                'id_rol' => 2,
            ],
            [
                'ci'     => '00000003',
                'nombre' => 'Recepcionista Prueba',
                'correo' => 'recepcion@taller.com',
                // Si la variable en el .env está vacía o no existe, se usa la clave por defecto
                'clave'  => env('RECEPCION_PASSWORD') ?: 'Recepcion1234!',
                'id_rol' => 3,
            ],
        ];

        foreach ($usuarios as $datos) {

            // ── 1. Insertar persona ───────────────────────────────
            DB::table('persona')->insertOrIgnore([
                'ci'          => $datos['ci'],
                'nombre'      => $datos['nombre'],
                'telefono'    => null,
                'direccion'   => null,
                'es_cliente'  => false,
                'es_personal' => true,
            ]);

            // ── 2. Generar nombre de usuario automático ───────────
            // Mismo algoritmo que UsuarioController::store()
            $partes         = explode(' ', strtolower(trim($datos['nombre'])));
            $primerNombre   = $partes[0];
            $primerApellido = $partes[1] ?? $partes[0];
            $base           = Str::slug(substr($primerNombre, 0, 1) . $primerApellido, '');

            $nombreUsuario = $base;
            $i = 1;
            while (DB::table('usuario')->where('nombre_usuario', $nombreUsuario)->exists()) {
                $nombreUsuario = $base . $i;
                $i++;
            }

            // ── 3. Insertar usuario ───────────────────────────────
            DB::table('usuario')->insertOrIgnore([
                'id_rol'         => $datos['id_rol'],
                'ci_personal'    => $datos['ci'],
                'nombre_usuario' => $nombreUsuario,
                'clave'          => Hash::make($datos['clave']),
                'correo'         => $datos['correo'],
            ]);
        }
    }
}