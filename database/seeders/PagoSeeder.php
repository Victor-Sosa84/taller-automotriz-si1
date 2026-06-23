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
        // Sueldo fijo: no depende de 'realiza', se mantienen los pagos semanales
        // tal como se diseñaron originalmente.
        $pagos = [
            ['id' => 1,  'id_contrato' => 2, 'fecha_pago' => '2026-05-08 18:00:00', 'monto' => 800.00, 'tipo' => 'Sueldo Semanal', 'metodo' => 'Efectivo'],
            ['id' => 2,  'id_contrato' => 7, 'fecha_pago' => '2026-05-08 18:05:00', 'monto' => 600.00, 'tipo' => 'Sueldo Semanal', 'metodo' => 'Efectivo'],
            ['id' => 5,  'id_contrato' => 2, 'fecha_pago' => '2026-05-15 17:30:00', 'monto' => 800.00, 'tipo' => 'Sueldo Semanal', 'metodo' => 'Efectivo'],
            ['id' => 6,  'id_contrato' => 7, 'fecha_pago' => '2026-05-15 17:35:00', 'monto' => 600.00, 'tipo' => 'Sueldo Semanal', 'metodo' => 'Efectivo'],
            ['id' => 9,  'id_contrato' => 2, 'fecha_pago' => '2026-05-22 18:00:00', 'monto' => 800.00, 'tipo' => 'Sueldo Semanal', 'metodo' => 'Efectivo'],
            ['id' => 10, 'id_contrato' => 8, 'fecha_pago' => '2026-05-22 18:10:00', 'monto' => 650.00, 'tipo' => 'Sueldo Semanal', 'metodo' => 'Transferencia'],
            ['id' => 12, 'id_contrato' => 2, 'fecha_pago' => '2026-05-29 17:00:00', 'monto' => 800.00, 'tipo' => 'Sueldo Semanal', 'metodo' => 'Efectivo'],
            ['id' => 13, 'id_contrato' => 9, 'fecha_pago' => '2026-05-29 17:15:00', 'monto' => 620.00, 'tipo' => 'Sueldo Semanal', 'metodo' => 'Efectivo'],
            ['id' => 15, 'id_contrato' => 2, 'fecha_pago' => '2026-06-05 18:00:00', 'monto' => 800.00, 'tipo' => 'Sueldo Semanal', 'metodo' => 'Efectivo'],
        ];

        DB::table('pago')->insert($pagos);

        // Comisión: cada monto corresponde exactamente a la suma de
        // (mano_obra.costo_referencial * porcentaje del contrato) sobre todos
        // los trabajos en 'realiza' de esa persona, ya que aquí no hay
        // liquidaciones previas que excluir. Se liquida en una sola pasada
        // por persona, y esos registros de 'realiza' quedan marcados como
        // pagados, consistentes con la lógica real de PagoController::calcularPago().
        DB::table('pago')->insert([
            ['id' => 18, 'id_contrato' => 3, 'fecha_pago' => '2026-06-19 18:00:00', 'monto' => 202.50, 'tipo' => 'Comisión', 'metodo' => 'Transferencia'],
            ['id' => 19, 'id_contrato' => 5, 'fecha_pago' => '2026-06-19 18:10:00', 'monto' => 230.00, 'tipo' => 'Comisión', 'metodo' => 'Transferencia'],
            ['id' => 20, 'id_contrato' => 6, 'fecha_pago' => '2026-06-19 18:20:00', 'monto' => 137.50, 'tipo' => 'Comisión', 'metodo' => 'Efectivo'],
        ]);

        DB::table('realiza')
            ->whereIn('ci_personal', ['6739154', '8214673', '5390218'])
            ->update(['pagado' => true]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}