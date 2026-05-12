<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermisoSeeder extends Seeder
{
    public function run(): void
    {
        $hoy = now()->toDateString();

        $permisos = [

            // ════════════════════════════════════════════════════
            // P1: GESTIÓN DE RECEPCIÓN — Ciclo 1
            // ════════════════════════════════════════════════════
            ['id' =>  1, 'nombre' => 'CU01_ADD', 'etiqueta' => 'Registrar Cliente',          'caso_uso' => 'CU-01', 'paquete' => 'P1: Gestión de Recepción'],
            ['id' =>  2, 'nombre' => 'CU01_MOD', 'etiqueta' => 'Modificar Cliente',           'caso_uso' => 'CU-01', 'paquete' => 'P1: Gestión de Recepción'],
            ['id' =>  3, 'nombre' => 'CU01_BUS', 'etiqueta' => 'Buscar/Ver Clientes',         'caso_uso' => 'CU-01', 'paquete' => 'P1: Gestión de Recepción'],
            ['id' =>  4, 'nombre' => 'CU02_ADD', 'etiqueta' => 'Registrar Vehículo',          'caso_uso' => 'CU-02', 'paquete' => 'P1: Gestión de Recepción'],
            ['id' =>  5, 'nombre' => 'CU02_MOD', 'etiqueta' => 'Modificar Vehículo',          'caso_uso' => 'CU-02', 'paquete' => 'P1: Gestión de Recepción'],
            ['id' =>  6, 'nombre' => 'CU02_ELI', 'etiqueta' => 'Eliminar Vehículo',           'caso_uso' => 'CU-02', 'paquete' => 'P1: Gestión de Recepción'],
            ['id' =>  7, 'nombre' => 'CU02_BUS', 'etiqueta' => 'Buscar/Ver Vehículos',        'caso_uso' => 'CU-02', 'paquete' => 'P1: Gestión de Recepción'],
            ['id' =>  8, 'nombre' => 'CU03_BUS', 'etiqueta' => 'Consultar Historial',         'caso_uso' => 'CU-03', 'paquete' => 'P1: Gestión de Recepción'],

            // P1 — Ciclo 2
            ['id' => 17, 'nombre' => 'CU04_ADD', 'etiqueta' => 'Registrar Ingreso de Unidad', 'caso_uso' => 'CU-04', 'paquete' => 'P1: Gestión de Recepción'],
            ['id' => 18, 'nombre' => 'CU04_MOD', 'etiqueta' => 'Modificar Ingreso de Unidad', 'caso_uso' => 'CU-04', 'paquete' => 'P1: Gestión de Recepción'],
            ['id' => 19, 'nombre' => 'CU04_BUS', 'etiqueta' => 'Ver Ingresos de Unidad',      'caso_uso' => 'CU-04', 'paquete' => 'P1: Gestión de Recepción'],
            ['id' => 20, 'nombre' => 'CU05_ADD', 'etiqueta' => 'Registrar Diagnóstico',       'caso_uso' => 'CU-05', 'paquete' => 'P1: Gestión de Recepción'],
            ['id' => 21, 'nombre' => 'CU05_MOD', 'etiqueta' => 'Modificar Diagnóstico',       'caso_uso' => 'CU-05', 'paquete' => 'P1: Gestión de Recepción'],
            ['id' => 22, 'nombre' => 'CU05_BUS', 'etiqueta' => 'Ver Diagnósticos',            'caso_uso' => 'CU-05', 'paquete' => 'P1: Gestión de Recepción'],

            // ════════════════════════════════════════════════════
            // P2: GESTIÓN COMERCIAL Y FACTURACIÓN — Ciclo 2
            // ════════════════════════════════════════════════════
            ['id' => 23, 'nombre' => 'CU06_ADD', 'etiqueta' => 'Elaborar Proforma',           'caso_uso' => 'CU-06', 'paquete' => 'P2: Gestión Comercial y Facturación'],
            ['id' => 24, 'nombre' => 'CU06_MOD', 'etiqueta' => 'Modificar Proforma',          'caso_uso' => 'CU-06', 'paquete' => 'P2: Gestión Comercial y Facturación'],
            ['id' => 25, 'nombre' => 'CU06_ELI', 'etiqueta' => 'Eliminar Proforma',           'caso_uso' => 'CU-06', 'paquete' => 'P2: Gestión Comercial y Facturación'],
            ['id' => 26, 'nombre' => 'CU06_BUS', 'etiqueta' => 'Ver Proformas',               'caso_uso' => 'CU-06', 'paquete' => 'P2: Gestión Comercial y Facturación'],
            ['id' => 27, 'nombre' => 'CU07_GEN', 'etiqueta' => 'Emitir Cotización',           'caso_uso' => 'CU-07', 'paquete' => 'P2: Gestión Comercial y Facturación'],
            ['id' => 28, 'nombre' => 'CU08_MOD', 'etiqueta' => 'Cambiar Estado de Proforma',  'caso_uso' => 'CU-08', 'paquete' => 'P2: Gestión Comercial y Facturación'],
            ['id' => 29, 'nombre' => 'CU08_BUS', 'etiqueta' => 'Ver Estado de Proformas',     'caso_uso' => 'CU-08', 'paquete' => 'P2: Gestión Comercial y Facturación'],

            // P2 — Ciclo 4 (estético)
            ['id' => 60, 'nombre' => 'CU17_GEN', 'etiqueta' => 'Generar Factura Final',       'caso_uso' => 'CU-17', 'paquete' => 'P2: Gestión Comercial y Facturación'],
            ['id' => 61, 'nombre' => 'CU17_BUS', 'etiqueta' => 'Ver Facturas',                'caso_uso' => 'CU-17', 'paquete' => 'P2: Gestión Comercial y Facturación'],
            ['id' => 62, 'nombre' => 'CU18_ADD', 'etiqueta' => 'Registrar Pago',              'caso_uso' => 'CU-18', 'paquete' => 'P2: Gestión Comercial y Facturación'],
            ['id' => 63, 'nombre' => 'CU18_BUS', 'etiqueta' => 'Ver Pagos y Cuotas',          'caso_uso' => 'CU-18', 'paquete' => 'P2: Gestión Comercial y Facturación'],

            // ════════════════════════════════════════════════════
            // P3: GESTIÓN ADMINISTRATIVA — Ciclo 1
            // ════════════════════════════════════════════════════
            ['id' =>  9, 'nombre' => 'CU13_ADD', 'etiqueta' => 'Crear Usuario',               'caso_uso' => 'CU-13', 'paquete' => 'P3: Gestión Administrativa'],
            ['id' => 10, 'nombre' => 'CU13_MOD', 'etiqueta' => 'Modificar Usuario',           'caso_uso' => 'CU-13', 'paquete' => 'P3: Gestión Administrativa'],
            ['id' => 11, 'nombre' => 'CU13_ELI', 'etiqueta' => 'Eliminar Usuario',            'caso_uso' => 'CU-13', 'paquete' => 'P3: Gestión Administrativa'],
            ['id' => 12, 'nombre' => 'CU13_BUS', 'etiqueta' => 'Ver Usuarios',                'caso_uso' => 'CU-13', 'paquete' => 'P3: Gestión Administrativa'],
            ['id' => 13, 'nombre' => 'CU13_PRI', 'etiqueta' => 'Gestionar Privilegios',       'caso_uso' => 'CU-13', 'paquete' => 'P3: Gestión Administrativa'],
            ['id' => 14, 'nombre' => 'CU19_LOG', 'etiqueta' => 'Iniciar Sesión',              'caso_uso' => 'CU-19', 'paquete' => 'P3: Gestión Administrativa'],
            ['id' => 15, 'nombre' => 'CU20_LOG', 'etiqueta' => 'Cerrar Sesión',               'caso_uso' => 'CU-20', 'paquete' => 'P3: Gestión Administrativa'],
            ['id' => 16, 'nombre' => 'CU21_BUS', 'etiqueta' => 'Consultar Bitácora',          'caso_uso' => 'CU-21', 'paquete' => 'P3: Gestión Administrativa'],

            // P3 — Ciclo 4 (estético)
            ['id' => 50, 'nombre' => 'CU11_ADD', 'etiqueta' => 'Registrar Contrato',          'caso_uso' => 'CU-11', 'paquete' => 'P3: Gestión Administrativa'],
            ['id' => 51, 'nombre' => 'CU11_MOD', 'etiqueta' => 'Modificar Contrato',          'caso_uso' => 'CU-11', 'paquete' => 'P3: Gestión Administrativa'],
            ['id' => 52, 'nombre' => 'CU11_ELI', 'etiqueta' => 'Eliminar Contrato',           'caso_uso' => 'CU-11', 'paquete' => 'P3: Gestión Administrativa'],
            ['id' => 53, 'nombre' => 'CU11_BUS', 'etiqueta' => 'Ver Contratos',               'caso_uso' => 'CU-11', 'paquete' => 'P3: Gestión Administrativa'],
            ['id' => 54, 'nombre' => 'CU12_ADD', 'etiqueta' => 'Registrar Liquidación',       'caso_uso' => 'CU-12', 'paquete' => 'P3: Gestión Administrativa'],
            ['id' => 55, 'nombre' => 'CU12_BUS', 'etiqueta' => 'Ver Liquidaciones',           'caso_uso' => 'CU-12', 'paquete' => 'P3: Gestión Administrativa'],

            // ════════════════════════════════════════════════════
            // P4: GESTIÓN OPERATIVA — Ciclo 3 (estético)
            // ════════════════════════════════════════════════════
            ['id' => 30, 'nombre' => 'CU14_ADD', 'etiqueta' => 'Crear Orden de Trabajo',      'caso_uso' => 'CU-14', 'paquete' => 'P4: Gestión Operativa'],
            ['id' => 31, 'nombre' => 'CU14_MOD', 'etiqueta' => 'Modificar Orden de Trabajo',  'caso_uso' => 'CU-14', 'paquete' => 'P4: Gestión Operativa'],
            ['id' => 32, 'nombre' => 'CU14_BUS', 'etiqueta' => 'Ver Órdenes de Trabajo',      'caso_uso' => 'CU-14', 'paquete' => 'P4: Gestión Operativa'],
            ['id' => 33, 'nombre' => 'CU14_EST', 'etiqueta' => 'Cambiar Estado de OT',        'caso_uso' => 'CU-14', 'paquete' => 'P4: Gestión Operativa'],
            ['id' => 34, 'nombre' => 'CU15_ADD', 'etiqueta' => 'Asignar Responsable a Tarea', 'caso_uso' => 'CU-15', 'paquete' => 'P4: Gestión Operativa'],
            ['id' => 35, 'nombre' => 'CU15_MOD', 'etiqueta' => 'Modificar Asignación',        'caso_uso' => 'CU-15', 'paquete' => 'P4: Gestión Operativa'],
            ['id' => 36, 'nombre' => 'CU15_BUS', 'etiqueta' => 'Ver Asignaciones',            'caso_uso' => 'CU-15', 'paquete' => 'P4: Gestión Operativa'],
            ['id' => 37, 'nombre' => 'CU16_ADD', 'etiqueta' => 'Registrar Repuesto/Mano Obra','caso_uso' => 'CU-16', 'paquete' => 'P4: Gestión Operativa'],
            ['id' => 38, 'nombre' => 'CU16_MOD', 'etiqueta' => 'Modificar Repuesto/Mano Obra','caso_uso' => 'CU-16', 'paquete' => 'P4: Gestión Operativa'],
            ['id' => 39, 'nombre' => 'CU16_BUS', 'etiqueta' => 'Ver Repuestos/Mano de Obra',  'caso_uso' => 'CU-16', 'paquete' => 'P4: Gestión Operativa'],
            ['id' => 40, 'nombre' => 'CU09_ADD', 'etiqueta' => 'Registrar Préstamo',          'caso_uso' => 'CU-09', 'paquete' => 'P4: Gestión Operativa'],
            ['id' => 41, 'nombre' => 'CU09_MOD', 'etiqueta' => 'Modificar Préstamo',          'caso_uso' => 'CU-09', 'paquete' => 'P4: Gestión Operativa'],
            ['id' => 42, 'nombre' => 'CU09_BUS', 'etiqueta' => 'Ver Préstamos',               'caso_uso' => 'CU-09', 'paquete' => 'P4: Gestión Operativa'],
            ['id' => 43, 'nombre' => 'CU10_ADD', 'etiqueta' => 'Registrar Devolución',        'caso_uso' => 'CU-10', 'paquete' => 'P4: Gestión Operativa'],
            ['id' => 44, 'nombre' => 'CU10_MOD', 'etiqueta' => 'Modificar Estado Herramienta','caso_uso' => 'CU-10', 'paquete' => 'P4: Gestión Operativa'],
            ['id' => 45, 'nombre' => 'CU10_BUS', 'etiqueta' => 'Ver Estado de Herramientas',  'caso_uso' => 'CU-10', 'paquete' => 'P4: Gestión Operativa'],
        ];

        DB::table('permiso')->insertOrIgnore($permisos);

        // Asignar todos al rol id=1 (Administrador del Sistema)
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