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
            HerramientaSeeder::class, 
            CatalogoSeeder::class,

            // ── Nivel 2: depende de rol + persona ─────────────────
            AdminSeeder::class,            // siembra persona + administrador
            DatosPruebaSeeder::class,      // clientes y autos de prueba
            RolPersonalSeeder::class,      // roles Mecánico/Recepcionista — depende de permiso
            PersonalSeeder::class,         // personal de prueba — depende de tipo_trabajador
            UsuarioPersonalSeeder::class,  // usuarios del personal — depende de rol + persona
            FlujoOperativoSeeder::class,   // diagnóstico→proforma→OT→realiza→factura→cuota — depende de persona/auto/mecánicos


            ContratoSeeder::class,         // contratos de prueba — depende de persona + tipo_remuneracion
            PagoSeeder::class,             // pagos de prueba — depende de contrato + realiza   

        ]);

    }
}