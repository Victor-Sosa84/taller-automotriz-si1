<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermisoSeeder extends Seeder
{
    public function run(): void
    {
        $hoy = now()->toDateString();

        // ── PERMISOS ─────────────────────────────────────────────
        // Usando insertOrIgnore para poder re-ejecutar sin borrar asignaciones
        // Al avanzar ciclos, solo AGREGAR permisos nuevos al final de cada grupo

        $permisos = [

            // ════════════════════════════════════════════════════
            // CICLO 1 — Base y Seguridad
            // ════════════════════════════════════════════════════

            // P1: Gestión de Recepción ───────────────────────────
            // CU-01: Gestionar Cliente
            ['id' =>  1, 'nombre' => 'CU01_ADD', 'etiqueta' => 'Registrar Cliente',         'caso_uso' => 'CU-01', 'paquete' => 'P1: Gestión de Recepción'],
            ['id' =>  2, 'nombre' => 'CU01_MOD', 'etiqueta' => 'Modificar Cliente',          'caso_uso' => 'CU-01', 'paquete' => 'P1: Gestión de Recepción'],
            ['id' =>  3, 'nombre' => 'CU01_BUS', 'etiqueta' => 'Buscar/Ver Clientes',        'caso_uso' => 'CU-01', 'paquete' => 'P1: Gestión de Recepción'],

            // CU-02: Gestionar Ficha Técnica de Vehículo
            ['id' =>  4, 'nombre' => 'CU02_ADD', 'etiqueta' => 'Registrar Vehículo',         'caso_uso' => 'CU-02', 'paquete' => 'P1: Gestión de Recepción'],
            ['id' =>  5, 'nombre' => 'CU02_MOD', 'etiqueta' => 'Modificar Vehículo',         'caso_uso' => 'CU-02', 'paquete' => 'P1: Gestión de Recepción'],
            ['id' =>  6, 'nombre' => 'CU02_ELI', 'etiqueta' => 'Eliminar Vehículo',          'caso_uso' => 'CU-02', 'paquete' => 'P1: Gestión de Recepción'],
            ['id' =>  7, 'nombre' => 'CU02_BUS', 'etiqueta' => 'Buscar/Ver Vehículos',       'caso_uso' => 'CU-02', 'paquete' => 'P1: Gestión de Recepción'],

            // CU-03: Consultar Historial de Mantenimiento
            ['id' =>  8, 'nombre' => 'CU03_BUS', 'etiqueta' => 'Consultar Historial',        'caso_uso' => 'CU-03', 'paquete' => 'P1: Gestión de Recepción'],

            // P3: Gestión Administrativa ─────────────────────────
            // CU-13: Gestionar Usuarios y Permisos
            ['id' =>  9, 'nombre' => 'CU13_ADD', 'etiqueta' => 'Crear Usuario',              'caso_uso' => 'CU-13', 'paquete' => 'P3: Gestión Administrativa'],
            ['id' => 10, 'nombre' => 'CU13_MOD', 'etiqueta' => 'Modificar Usuario',          'caso_uso' => 'CU-13', 'paquete' => 'P3: Gestión Administrativa'],
            ['id' => 11, 'nombre' => 'CU13_ELI', 'etiqueta' => 'Eliminar Usuario',           'caso_uso' => 'CU-13', 'paquete' => 'P3: Gestión Administrativa'],
            ['id' => 12, 'nombre' => 'CU13_BUS', 'etiqueta' => 'Ver Usuarios',               'caso_uso' => 'CU-13', 'paquete' => 'P3: Gestión Administrativa'],
            ['id' => 13, 'nombre' => 'CU13_PRI', 'etiqueta' => 'Gestionar Privilegios',      'caso_uso' => 'CU-13', 'paquete' => 'P3: Gestión Administrativa'],

            // CU-19: Iniciar Sesión
            ['id' => 14, 'nombre' => 'CU19_LOG', 'etiqueta' => 'Iniciar Sesión',             'caso_uso' => 'CU-19', 'paquete' => 'P3: Gestión Administrativa'],

            // CU-20: Cerrar Sesión
            ['id' => 15, 'nombre' => 'CU20_LOG', 'etiqueta' => 'Cerrar Sesión',              'caso_uso' => 'CU-20', 'paquete' => 'P3: Gestión Administrativa'],

            // CU-21: Consultar Bitácora
            ['id' => 16, 'nombre' => 'CU21_BUS', 'etiqueta' => 'Consultar Bitácora',         'caso_uso' => 'CU-21', 'paquete' => 'P3: Gestión Administrativa'],

            // ════════════════════════════════════════════════════
            // CICLO 2 — Recepción y Presupuesto
            // ════════════════════════════════════════════════════

            // P1: Gestión de Recepción ───────────────────────────
            // CU-04: Gestionar Ingreso de Unidad
            ['id' => 17, 'nombre' => 'CU04_ADD', 'etiqueta' => 'Registrar Ingreso de Unidad','caso_uso' => 'CU-04', 'paquete' => 'P1: Gestión de Recepción'],
            ['id' => 18, 'nombre' => 'CU04_MOD', 'etiqueta' => 'Modificar Ingreso de Unidad','caso_uso' => 'CU-04', 'paquete' => 'P1: Gestión de Recepción'],
            ['id' => 19, 'nombre' => 'CU04_BUS', 'etiqueta' => 'Ver Ingresos de Unidad',     'caso_uso' => 'CU-04', 'paquete' => 'P1: Gestión de Recepción'],

            // CU-05: Realizar Diagnóstico Técnico
            ['id' => 20, 'nombre' => 'CU05_ADD', 'etiqueta' => 'Registrar Diagnóstico',      'caso_uso' => 'CU-05', 'paquete' => 'P1: Gestión de Recepción'],
            ['id' => 21, 'nombre' => 'CU05_MOD', 'etiqueta' => 'Modificar Diagnóstico',      'caso_uso' => 'CU-05', 'paquete' => 'P1: Gestión de Recepción'],
            ['id' => 22, 'nombre' => 'CU05_BUS', 'etiqueta' => 'Ver Diagnósticos',           'caso_uso' => 'CU-05', 'paquete' => 'P1: Gestión de Recepción'],

            // P2: Gestión Comercial y Facturación ────────────────
            // CU-06: Elaborar Proforma
            ['id' => 23, 'nombre' => 'CU06_ADD', 'etiqueta' => 'Elaborar Proforma',          'caso_uso' => 'CU-06', 'paquete' => 'P2: Gestión Comercial y Facturación'],
            ['id' => 24, 'nombre' => 'CU06_MOD', 'etiqueta' => 'Modificar Proforma',         'caso_uso' => 'CU-06', 'paquete' => 'P2: Gestión Comercial y Facturación'],
            ['id' => 25, 'nombre' => 'CU06_ELI', 'etiqueta' => 'Eliminar Proforma',          'caso_uso' => 'CU-06', 'paquete' => 'P2: Gestión Comercial y Facturación'],
            ['id' => 26, 'nombre' => 'CU06_BUS', 'etiqueta' => 'Ver Proformas',              'caso_uso' => 'CU-06', 'paquete' => 'P2: Gestión Comercial y Facturación'],

            // CU-07: Emitir Cotización
            ['id' => 27, 'nombre' => 'CU07_GEN', 'etiqueta' => 'Emitir Cotización',          'caso_uso' => 'CU-07', 'paquete' => 'P2: Gestión Comercial y Facturación'],

            // CU-08: Gestionar Estado de Proforma
            ['id' => 28, 'nombre' => 'CU08_MOD', 'etiqueta' => 'Cambiar Estado de Proforma', 'caso_uso' => 'CU-08', 'paquete' => 'P2: Gestión Comercial y Facturación'],
            ['id' => 29, 'nombre' => 'CU08_BUS', 'etiqueta' => 'Ver Estado de Proformas',    'caso_uso' => 'CU-08', 'paquete' => 'P2: Gestión Comercial y Facturación'],

            // ════════════════════════════════════════════════════
            // CICLO 3 — Gestión Operativa (agregar cuando llegue el momento)
            // IDs reservados: 30-50
            // ════════════════════════════════════════════════════

            // ════════════════════════════════════════════════════
            // CICLO 4 — Liquidación y Salida (agregar cuando llegue el momento)
            // IDs reservados: 51-70
            // ════════════════════════════════════════════════════
        ];

        DB::table('permiso')->insertOrIgnore($permisos);

        // ── Asignar TODOS los permisos al rol Administrador (id=1) ──
        // El usuario id=1 no necesita esto (bypassa todo), pero el rol
        // Administrador debe tenerlos para cuando se asignen a otros roles
        foreach ($permisos as $p) {
            DB::table('rol_permiso')->insertOrIgnore([
                'id_permiso'     => $p['id'],
                'id_rol'         => 1,
                'estado'         => 'Activo',
                'fecha_registro' => $hoy,
                'observaciones'  => null,
            ]);
        }
    }
}