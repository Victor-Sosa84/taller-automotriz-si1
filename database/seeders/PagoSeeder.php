<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PagoSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('realiza')->truncate();
        DB::table('pago')->truncate();

        // =========================================================================
        // 1. POBLAR TABLA: REALIZA (Agregando id_mano_obra para evitar el error 1364)
        // =========================================================================
        $trabajos = [
            ['nro_orden_trabajo' => 1,  'ci_personal' => '4521837', 'id_mano_obra' => 1, 'tipo_participacion' => 'Mecánico Principal'],
            ['nro_orden_trabajo' => 2,  'ci_personal' => '6739154', 'id_mano_obra' => 2, 'tipo_participacion' => 'Especialista Eléctrico'],
            ['nro_orden_trabajo' => 3,  'ci_personal' => '8214673', 'id_mano_obra' => 3, 'tipo_participacion' => 'Mecánico Principal'],
            ['nro_orden_trabajo' => 4,  'ci_personal' => '5390218', 'id_mano_obra' => 4, 'tipo_participacion' => 'Asistente Técnico'],
            ['nro_orden_trabajo' => 5,  'ci_personal' => '4521837', 'id_mano_obra' => 1, 'tipo_participacion' => 'Mecánico Principal'],
            ['nro_orden_trabajo' => 6,  'ci_personal' => '6739154', 'id_mano_obra' => 2, 'tipo_participacion' => 'Mecánico Principal'],
            ['nro_orden_trabajo' => 7,  'ci_personal' => '8214673', 'id_mano_obra' => 5, 'tipo_participacion' => 'Especialista Transmisiones'],
            ['nro_orden_trabajo' => 8,  'ci_personal' => '5390218', 'id_mano_obra' => 4, 'tipo_participacion' => 'Mecánico Principal'],
            ['nro_orden_trabajo' => 9,  'ci_personal' => '4521837', 'id_mano_obra' => 6, 'tipo_participacion' => 'Diagnosticador'],
            ['nro_orden_trabajo' => 10, 'ci_personal' => '6739154', 'id_mano_obra' => 2, 'tipo_participacion' => 'Especialista Eléctrico'],
            ['nro_orden_trabajo' => 11, 'ci_personal' => '8214673', 'id_mano_obra' => 1, 'tipo_participacion' => 'Mecánico Principal'],
            ['nro_orden_trabajo' => 12, 'ci_personal' => '5390218', 'id_mano_obra' => 4, 'tipo_participacion' => 'Asistente Técnico'],
            ['nro_orden_trabajo' => 13, 'ci_personal' => '4521837', 'id_mano_obra' => 3, 'tipo_participacion' => 'Mecánico Principal'],
            ['nro_orden_trabajo' => 14, 'ci_personal' => '6739154', 'id_mano_obra' => 1, 'tipo_participacion' => 'Mecánico Principal'],
            ['nro_orden_trabajo' => 15, 'ci_personal' => '8214673', 'id_mano_obra' => 5, 'tipo_participacion' => 'Especialista Suspensión'],
            ['nro_orden_trabajo' => 16, 'ci_personal' => '5390218', 'id_mano_obra' => 2, 'tipo_participacion' => 'Mecánico Principal'],
        ];
        DB::table('realiza')->insert($trabajos);

        // =========================================================================
        // 2. POBLAR TABLA: PAGO 
        // =========================================================================
        $pagos = [
            ['id' => 1,  'id_contrato' => 2, 'fecha_pago' => '2026-05-08 18:00:00', 'monto' => 800.00, 'tipo' => 'Sueldo Semanal', 'metodo' => 'Efectivo'],
            ['id' => 2,  'id_contrato' => 7, 'fecha_pago' => '2026-05-08 18:05:00', 'monto' => 600.00, 'tipo' => 'Sueldo Semanal', 'metodo' => 'Efectivo'],
            ['id' => 3,  'id_contrato' => 3, 'fecha_pago' => '2026-05-08 18:10:00', 'monto' => 450.00, 'tipo' => 'Comisión Semanal', 'metodo' => 'Transferencia'],
            ['id' => 4,  'id_contrato' => 5, 'fecha_pago' => '2026-05-08 18:15:00', 'monto' => 520.00, 'tipo' => 'Comisión Semanal', 'metodo' => 'Transferencia'],
            ['id' => 5,  'id_contrato' => 2, 'fecha_pago' => '2026-05-15 17:30:00', 'monto' => 800.00, 'tipo' => 'Sueldo Semanal', 'metodo' => 'Efectivo'],
            ['id' => 6,  'id_contrato' => 7, 'fecha_pago' => '2026-05-15 17:35:00', 'monto' => 600.00, 'tipo' => 'Sueldo Semanal', 'metodo' => 'Efectivo'],
            ['id' => 7,  'id_contrato' => 3, 'fecha_pago' => '2026-05-15 17:40:00', 'monto' => 380.00, 'tipo' => 'Comisión Semanal', 'metodo' => 'Transferencia'],
            ['id' => 8,  'id_contrato' => 6, 'fecha_pago' => '2026-05-15 17:45:00', 'monto' => 410.00, 'tipo' => 'Comisión Semanal', 'metodo' => 'Efectivo'],
            ['id' => 9,  'id_contrato' => 2, 'fecha_pago' => '2026-05-22 18:00:00', 'monto' => 800.00, 'tipo' => 'Sueldo Semanal', 'metodo' => 'Efectivo'],
            ['id' => 10, 'id_contrato' => 8, 'fecha_pago' => '2026-05-22 18:10:00', 'monto' => 650.00, 'tipo' => 'Sueldo Semanal', 'metodo' => 'Transferencia'],
            ['id' => 11, 'id_contrato' => 5, 'fecha_pago' => '2026-05-22 18:20:00', 'monto' => 610.00, 'tipo' => 'Comisión Semanal', 'metodo' => 'Transferencia'],
            ['id' => 12, 'id_contrato' => 2, 'fecha_pago' => '2026-05-29 17:00:00', 'monto' => 800.00, 'tipo' => 'Sueldo Semanal', 'metodo' => 'Efectivo'],
            ['id' => 13, 'id_contrato' => 9, 'fecha_pago' => '2026-05-29 17:15:00', 'monto' => 620.00, 'tipo' => 'Sueldo Semanal', 'metodo' => 'Efectivo'],
            ['id' => 14, 'id_contrato' => 3, 'fecha_pago' => '2026-05-29 17:30:00', 'monto' => 490.00, 'tipo' => 'Comisión Semanal', 'metodo' => 'Transferencia'],
            ['id' => 15, 'id_contrato' => 2, 'fecha_pago' => '2026-06-05 18:00:00', 'monto' => 800.00, 'tipo' => 'Sueldo Semanal', 'metodo' => 'Efectivo'],
            ['id' => 16, 'id_contrato' => 5, 'fecha_pago' => '2026-06-12 18:00:00', 'monto' => 570.00, 'tipo' => 'Comisión Semanal', 'metodo' => 'Transferencia'],
            ['id' => 17, 'id_contrato' => 6, 'fecha_pago' => '2026-06-19 18:00:00', 'monto' => 630.00, 'tipo' => 'Comisión Semanal', 'metodo' => 'Efectivo'],
        ];

        DB::table('pago')->insert($pagos);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
