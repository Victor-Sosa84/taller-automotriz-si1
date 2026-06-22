<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolPersonalSeeder extends Seeder
{
    public function run(): void
    {
        $hoy = now()->toDateString();

        // Roles dinámicos — se pueden crear más desde la interfaz.
        $roles = [
            ['id' => 2, 'nombre' => 'Mecánico',     'descripcion' => 'Realiza diagnósticos, ejecuta trabajos y gestiona herramientas.'],
            ['id' => 3, 'nombre' => 'Recepcionista', 'descripcion' => 'Atiende clientes, gestiona ingresos, proformas, OT, facturación y préstamos.'],
        ];

        DB::table('rol')->insertOrIgnore($roles);

        // Permisos por rol, según actor(es) listado en la ficha de cada CU.
        $permisosMecanico = [
            'CU03_BUS',                                   // Consultar historial — "cualquier usuario autenticado"
            'CU05_ADD', 'CU05_MOD', 'CU05_BUS',            // Diagnóstico — actor iniciador
            'CU09_BUS',                                   // Ver préstamos (ficha: actor=Recepcionista, pero el mecánico usa las herramientas)
            'CU10_BUS',                                   // Ver estado de herramientas (idem)
            'CU14_BUS', 'CU14_EST',                        // Ver OT y cambiar estado — actor(es) incluye Mecánico
            'CU16_ADD', 'CU16_MOD', 'CU16_BUS',            // Repuestos/Mano de obra — actor iniciador
            'CU22_GEN',                                     // Reportes por comando de voz — cualquier usuario autenticado
        ];

        $permisosRecepcionista = [
            'CU01_ADD', 'CU01_MOD', 'CU01_BUS',            // Cliente
            'CU02_ADD', 'CU02_MOD', 'CU02_ELI', 'CU02_BUS',// Vehículo
            'CU03_BUS',                                    // Historial
            'CU04_ADD', 'CU04_MOD', 'CU04_BUS',            // Ingreso de unidad
            'CU06_ADD', 'CU06_MOD', 'CU06_ELI', 'CU06_BUS',// Proforma
            'CU07_GEN',                                     // Emitir cotización
            'CU08_MOD', 'CU08_BUS',                         // Estado de proforma
            'CU09_ADD', 'CU09_MOD', 'CU09_BUS',             // Préstamo de herramientas
            'CU10_ADD', 'CU10_MOD', 'CU10_BUS',             // Estado de herramientas
            'CU14_ADD', 'CU14_MOD', 'CU14_BUS',             // Gestionar OT — actor iniciador
            'CU15_ADD', 'CU15_MOD', 'CU15_BUS', 'CU15_ELI', // Asignar responsables — actor(es) incluye Recepcionista
            'CU16_BUS',                                     // Ver repuestos/mano de obra — actor(es) incluye Recepcionista
            'CU17_GEN', 'CU17_BUS',                         // Generar factura — actor iniciador
            'CU18_ADD', 'CU18_BUS',                         // Pago y cuotas — actor iniciador
            'CU22_GEN',                                     // Reportes por comando de voz — cualquier usuario autenticado
        ];

        $idsPorNombre = DB::table('permiso')
            ->whereIn('nombre', array_unique(array_merge($permisosMecanico, $permisosRecepcionista)))
            ->pluck('id', 'nombre');

        $asignaciones = [];

        foreach ($permisosMecanico as $nombre) {
            $asignaciones[] = [
                'id_permiso'     => $idsPorNombre[$nombre],
                'id_rol'         => 2,
                'estado'         => 'Activo',
                'fecha_registro' => $hoy,
                'observaciones'  => null,
            ];
        }

        foreach ($permisosRecepcionista as $nombre) {
            $asignaciones[] = [
                'id_permiso'     => $idsPorNombre[$nombre],
                'id_rol'         => 3,
                'estado'         => 'Activo',
                'fecha_registro' => $hoy,
                'observaciones'  => null,
            ];
        }

        DB::table('rol_permiso')->insertOrIgnore($asignaciones);
    }
}
