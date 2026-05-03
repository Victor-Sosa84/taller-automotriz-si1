<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermisoSeeder extends Seeder
{
    public function run(): void
    {
        // ── Permisos completos del sistema ───────────────────────
        DB::table('permiso')->insert([
            // Clientes y Vehículos
            ['id' =>  1, 'nombre' => 'CLI_VIEW',    'etiqueta' => 'Ver Clientes',            'modulo' => 'Clientes y Vehículos'],
            ['id' =>  2, 'nombre' => 'CLI_CREATE',  'etiqueta' => 'Registrar Cliente',        'modulo' => 'Clientes y Vehículos'],
            ['id' =>  3, 'nombre' => 'CLI_EDIT',    'etiqueta' => 'Editar Cliente',           'modulo' => 'Clientes y Vehículos'],
            ['id' =>  4, 'nombre' => 'VEH_VIEW',    'etiqueta' => 'Ver Vehículos',            'modulo' => 'Clientes y Vehículos'],
            ['id' =>  5, 'nombre' => 'VEH_CREATE',  'etiqueta' => 'Registrar Vehículo',       'modulo' => 'Clientes y Vehículos'],
            ['id' =>  6, 'nombre' => 'VEH_EDIT',    'etiqueta' => 'Editar Vehículo',          'modulo' => 'Clientes y Vehículos'],
            ['id' =>  7, 'nombre' => 'VEH_DELETE',  'etiqueta' => 'Eliminar Vehículo',        'modulo' => 'Clientes y Vehículos'],
            ['id' =>  8, 'nombre' => 'HIST_VIEW',   'etiqueta' => 'Ver Historial',            'modulo' => 'Clientes y Vehículos'],
            // Recepción y Diagnóstico
            ['id' =>  9, 'nombre' => 'DIAG_VIEW',   'etiqueta' => 'Ver Diagnósticos',         'modulo' => 'Recepción y Diagnóstico'],
            ['id' => 10, 'nombre' => 'DIAG_CREATE', 'etiqueta' => 'Registrar Diagnóstico',    'modulo' => 'Recepción y Diagnóstico'],
            // Cotizaciones
            ['id' => 11, 'nombre' => 'PROF_VIEW',   'etiqueta' => 'Ver Proformas',            'modulo' => 'Cotizaciones'],
            ['id' => 12, 'nombre' => 'PROF_CREATE', 'etiqueta' => 'Crear Proforma',           'modulo' => 'Cotizaciones'],
            ['id' => 13, 'nombre' => 'PROF_EDIT',   'etiqueta' => 'Editar Proforma',          'modulo' => 'Cotizaciones'],
            // Inventario de Herramientas
            ['id' => 14, 'nombre' => 'HERR_VIEW',   'etiqueta' => 'Ver Herramientas',         'modulo' => 'Inventario'],
            ['id' => 15, 'nombre' => 'HERR_CREATE', 'etiqueta' => 'Registrar Herramienta',    'modulo' => 'Inventario'],
            ['id' => 16, 'nombre' => 'PREST_CREATE','etiqueta' => 'Registrar Préstamo',       'modulo' => 'Inventario'],
            ['id' => 17, 'nombre' => 'PREST_DEVOL', 'etiqueta' => 'Registrar Devolución',     'modulo' => 'Inventario'],
            // Órdenes de Trabajo
            ['id' => 18, 'nombre' => 'OT_VIEW',     'etiqueta' => 'Ver Órdenes de Trabajo',   'modulo' => 'Órdenes de Trabajo'],
            ['id' => 19, 'nombre' => 'OT_CREATE',   'etiqueta' => 'Crear Orden de Trabajo',   'modulo' => 'Órdenes de Trabajo'],
            ['id' => 20, 'nombre' => 'OT_EDIT',     'etiqueta' => 'Editar Orden de Trabajo',  'modulo' => 'Órdenes de Trabajo'],
            ['id' => 21, 'nombre' => 'OT_ESTADO',   'etiqueta' => 'Cambiar Estado de OT',     'modulo' => 'Órdenes de Trabajo'],
            // Facturación
            ['id' => 22, 'nombre' => 'FACT_VIEW',   'etiqueta' => 'Ver Facturas',             'modulo' => 'Facturación'],
            ['id' => 23, 'nombre' => 'FACT_CREATE', 'etiqueta' => 'Generar Factura',          'modulo' => 'Facturación'],
            ['id' => 24, 'nombre' => 'PAGO_CREATE', 'etiqueta' => 'Registrar Pago',           'modulo' => 'Facturación'],
            // Personal
            ['id' => 25, 'nombre' => 'PERS_VIEW',   'etiqueta' => 'Ver Personal',             'modulo' => 'Personal'],
            ['id' => 26, 'nombre' => 'PERS_EDIT',   'etiqueta' => 'Editar Personal',          'modulo' => 'Personal'],
            ['id' => 27, 'nombre' => 'CONT_VIEW',   'etiqueta' => 'Ver Contratos',            'modulo' => 'Personal'],
            ['id' => 28, 'nombre' => 'CONT_CREATE', 'etiqueta' => 'Crear Contrato',           'modulo' => 'Personal'],
            // Seguridad
            ['id' => 29, 'nombre' => 'USU_VIEW',    'etiqueta' => 'Ver Usuarios',             'modulo' => 'Seguridad'],
            ['id' => 30, 'nombre' => 'USU_CREATE',  'etiqueta' => 'Crear Usuario',            'modulo' => 'Seguridad'],
            ['id' => 31, 'nombre' => 'USU_EDIT',    'etiqueta' => 'Editar Usuario',           'modulo' => 'Seguridad'],
            ['id' => 32, 'nombre' => 'BIT_VIEW',    'etiqueta' => 'Ver Bitácora',             'modulo' => 'Seguridad'],
        ]);

        $hoy = now()->toDateString();

        // ── Asignación por rol ───────────────────────────────────
        // Rol 1 = Administrador → todos los permisos
        $todosLosPermisos = range(1, 32);
        foreach ($todosLosPermisos as $idPermiso) {
            DB::table('rol_permiso')->insert([
                'id_permiso'      => $idPermiso,
                'id_rol'          => 1,
                'estado'          => 'Activo',
                'fecha_registro'  => $hoy,
                'observaciones'   => null,
            ]);
        }

        // Rol 2 = Mecánico Jefe
        $permisosMetanico = [
            4,   // VEH_VIEW
            8,   // HIST_VIEW
            9,   // DIAG_VIEW
            10,  // DIAG_CREATE
            14,  // HERR_VIEW
            15,  // HERR_CREATE
            16,  // PREST_CREATE
            17,  // PREST_DEVOL
            18,  // OT_VIEW
            20,  // OT_EDIT
            21,  // OT_ESTADO
        ];
        foreach ($permisosMetanico as $idPermiso) {
            DB::table('rol_permiso')->insert([
                'id_permiso'      => $idPermiso,
                'id_rol'          => 2,
                'estado'          => 'Activo',
                'fecha_registro'  => $hoy,
                'observaciones'   => null,
            ]);
        }

        // Rol 3 = Recepcionista
        $permisosRecepcionista = [
            1,   // CLI_VIEW
            2,   // CLI_CREATE
            3,   // CLI_EDIT
            4,   // VEH_VIEW
            5,   // VEH_CREATE
            6,   // VEH_EDIT
            8,   // HIST_VIEW
            9,   // DIAG_VIEW
            11,  // PROF_VIEW
            12,  // PROF_CREATE
            13,  // PROF_EDIT
            22,  // FACT_VIEW
            23,  // FACT_CREATE
            24,  // PAGO_CREATE
        ];
        foreach ($permisosRecepcionista as $idPermiso) {
            DB::table('rol_permiso')->insert([
                'id_permiso'      => $idPermiso,
                'id_rol'          => 3,
                'estado'          => 'Activo',
                'fecha_registro'  => $hoy,
                'observaciones'   => null,
            ]);
        }
    }
}