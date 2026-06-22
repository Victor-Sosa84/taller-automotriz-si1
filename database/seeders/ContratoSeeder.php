<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContratoSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // 1. Poblamos la tabla maestra de tipo_remuneracion
        DB::table('tipo_remuneracion')->truncate();
        DB::table('tipo_remuneracion')->insert([
            ['nro' => 1, 'descripcion' => 'Sueldo Fijo'],
            ['nro' => 2, 'descripcion' => 'Porcentaje por Comisión'],
        ]);

        // 2. Limpiamos y poblamos la tabla contrato
        DB::table('contrato')->truncate();

        $contratos = [
            // ================= LUIS FERNANDO MAMANI (Mecánico) =================
            ['id' => 1, 'ci_personal' => '4521837', 'tipo_remuneracion' => 1, 'fecha_inicio' => '2025-01-01', 'fecha_fin' => '2025-06-30', 'estado' => 'Finalizado', 'periodo_pago' => 'Semanal', 'valor' => 700.00],
            ['id' => 2, 'ci_personal' => '4521837', 'tipo_remuneracion' => 1, 'fecha_inicio' => '2025-07-01', 'fecha_fin' => null, 'estado' => 'Vigente', 'periodo_pago' => 'Semanal', 'valor' => 800.00],

            // ================= HUGO ARIEL VACA (Mecánico) =================
            ['id' => 3, 'ci_personal' => '6739154', 'tipo_remuneracion' => 2, 'fecha_inicio' => '2025-02-15', 'fecha_fin' => null, 'estado' => 'Vigente', 'periodo_pago' => 'Semanal', 'valor' => 15.00],

            // ================= MARCO ANTONIO SALVATIERRA (Mecánico) =================
            ['id' => 4, 'ci_personal' => '8214673', 'tipo_remuneracion' => 1, 'fecha_inicio' => '2025-01-10', 'fecha_fin' => '2025-12-31', 'estado' => 'Finalizado', 'periodo_pago' => 'Semanal', 'valor' => 750.00],
            ['id' => 5, 'ci_personal' => '8214673', 'tipo_remuneracion' => 2, 'fecha_inicio' => '2026-01-01', 'fecha_fin' => null, 'estado' => 'Vigente', 'periodo_pago' => 'Semanal', 'valor' => 20.00],

            // ================= ESTEBAN DANIEL CHOQUE (Mecánico) =================
            ['id' => 6, 'ci_personal' => '5390218', 'tipo_remuneracion' => 2, 'fecha_inicio' => '2025-03-01', 'fecha_fin' => null, 'estado' => 'Vigente', 'periodo_pago' => 'Semanal', 'valor' => 12.50],

            // ================= DANIELA ANDREA ROCA (Recepcionista) =================
            ['id' => 7, 'ci_personal' => '7461923', 'tipo_remuneracion' => 1, 'fecha_inicio' => '2025-01-01', 'fecha_fin' => null, 'estado' => 'Vigente', 'periodo_pago' => 'Semanal', 'valor' => 600.00],

            // ================= PATRICIA LUCÍA VARGAS (Recepcionista) =================
            ['id' => 8, 'ci_personal' => '3957284', 'tipo_remuneracion' => 1, 'fecha_inicio' => '2025-04-15', 'fecha_fin' => null, 'estado' => 'Vigente', 'periodo_pago' => 'Semanal', 'valor' => 650.00],

            // ================= GABRIELA FERNANDA ÁÑEZ (Recepcionista) =================
            ['id' => 9, 'ci_personal' => '6082417', 'tipo_remuneracion' => 1, 'fecha_inicio' => '2025-05-01', 'fecha_fin' => null, 'estado' => 'Vigente', 'periodo_pago' => 'Semanal', 'valor' => 620.00],

            // ================= CONTRATOS ADICIONALES DE RESPALDO =================
            ['id' => 10, 'ci_personal' => '4521837', 'tipo_remuneracion' => 1, 'fecha_inicio' => '2024-01-01', 'fecha_fin' => '2024-06-30', 'estado' => 'Finalizado', 'periodo_pago' => 'Semanal', 'valor' => 500.00],
            ['id' => 11, 'ci_personal' => '6739154', 'tipo_remuneracion' => 1, 'fecha_inicio' => '2024-02-01', 'fecha_fin' => '2024-08-31', 'estado' => 'Finalizado', 'periodo_pago' => 'Semanal', 'valor' => 550.00],
            ['id' => 12, 'ci_personal' => '8214673', 'tipo_remuneracion' => 1, 'fecha_inicio' => '2024-03-01', 'fecha_fin' => '2024-09-30', 'estado' => 'Finalizado', 'periodo_pago' => 'Semanal', 'valor' => 580.00],
            ['id' => 13, 'ci_personal' => '5390218', 'tipo_remuneracion' => 1, 'fecha_inicio' => '2024-04-01', 'fecha_fin' => '2024-10-31', 'estado' => 'Finalizado', 'periodo_pago' => 'Semanal', 'valor' => 520.00],
            ['id' => 14, 'ci_personal' => '7461923', 'tipo_remuneracion' => 1, 'fecha_inicio' => '2024-05-01', 'fecha_fin' => '2024-11-30', 'estado' => 'Finalizado', 'periodo_pago' => 'Semanal', 'valor' => 480.00],
            ['id' => 15, 'ci_personal' => '3957284', 'tipo_remuneracion' => 1, 'fecha_inicio' => '2024-06-01', 'fecha_fin' => '2024-12-31', 'estado' => 'Finalizado', 'periodo_pago' => 'Semanal', 'valor' => 490.00],
            ['id' => 16, 'ci_personal' => '6082417', 'tipo_remuneracion' => 1, 'fecha_inicio' => '2024-07-01', 'fecha_fin' => '2024-12-31', 'estado' => 'Finalizado', 'periodo_pago' => 'Semanal', 'valor' => 510.00],
            ['id' => 17, 'ci_personal' => '4521837', 'tipo_remuneracion' => 2, 'fecha_inicio' => '2024-08-01', 'fecha_fin' => '2024-12-31', 'estado' => 'Finalizado', 'periodo_pago' => 'Semanal', 'valor' => 10.00],
        ];

        DB::table('contrato')->insert($contratos);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
