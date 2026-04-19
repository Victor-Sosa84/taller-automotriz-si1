<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // ── Nivel 1: sin dependencias ─────────────────────────
            RolSeeder::class,
            PermisoSeeder::class,          // también siembra rol_permiso
            TipoTrabajadorSeeder::class,
            TipoHerramientaSeeder::class,
            MarcaHerramientaSeeder::class,

            // ── Nivel 2: depende de rol + persona ─────────────────
            UsuarioSeeder::class,            // siembra persona + usuario
        ]);
    }
}
