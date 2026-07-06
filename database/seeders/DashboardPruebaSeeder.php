<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DashboardPruebaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Asegurar catálogo mínimo de repuestos si no existen
        DB::table('repuesto')->insertOrIgnore([
            ['id' => 1, 'nombre' => 'Pastillas de Freno Delanteras', 'estado' => 'Nuevo', 'marca' => 'Bosch', 'precio_referencial' => 45.00],
            ['id' => 2, 'nombre' => 'Filtro de Aceite Sintético', 'estado' => 'Nuevo', 'marca' => 'Fram', 'precio_referencial' => 15.00],
            ['id' => 3, 'nombre' => 'Batería 12V 13 Placas', 'estado' => 'Nuevo', 'marca' => 'Toyo', 'precio_referencial' => 110.00],
        ]);

        // 2. Asegurar catálogo mínimo de mano de obra
        DB::table('mano_obra')->insertOrIgnore([
            ['id' => 1, 'descripcion' => 'Cambio de Aceite y Filtros', 'costo_referencial' => 50.00],
            ['id' => 2, 'descripcion' => 'Mantenimiento de Sistema de Frenos', 'costo_referencial' => 80.00],
            ['id' => 3, 'descripcion' => 'Alineación y Balanceo Computarizado', 'costo_referencial' => 60.00],
        ]);

        // Variables de fecha HOY para que el DashboardController lo capture al instante
        $hoy = now()->format('Y-m-d H:i:s');
        $hoyFecha = now()->format('Y-m-d');

        // 3. Crear una proforma puente para hoy
        DB::table('proforma')->insertOrIgnore([
            'nro' => 999,
            'ci_cliente' => '7234891', // Carlos Alberto Romero (Existe en tus seeders)
            'id_diagnostico' => 1,      // Ajustar al ID existente o ignorar restricción si no hay FK estricta en base
            'fecha' => $hoy,
            'total_aprox' => 350.00,
            'estado' => 'Aprobada',
            'plazo' => $hoyFecha
        ]);

        // 4. INSERTAR ÓRDENES DE TRABAJO PARA HOY (Métrica 1)
        DB::table('orden_trabajo')->insertOrIgnore([
            [
                'nro' => 501,
                'nro_proforma' => 999,
                'fecha_inicio' => $hoy,
                'fecha_fin' => null,
                'estado' => 'En Proceso',
                'kilometraje' => 45000,
                'observacion_entrada' => 'Mantenimiento del Dashboard activo',
                'observacion_salida' => null
            ],
            [
                'nro' => 502,
                'nro_proforma' => 999,
                'fecha_inicio' => $hoy,
                'fecha_fin' => $hoy,
                'estado' => 'Finalizada',
                'kilometraje' => 120000,
                'observacion_entrada' => 'Revisión por ruido en frenos',
                'observacion_salida' => 'Pastillas cambiadas con éxito'
            ]
        ]);

        // 5. ASIGNAR MECÁNICOS A LAS ÓRDENES (Métrica 2 - Flujo Operativo Realiza)
        DB::table('realiza')->insertOrIgnore([
            // Mecánico Luis Fernando asignado a la orden 501
            ['ci_personal' => '4521837', 'nro_orden_trabajo' => 501, 'id_mano_obra' => 1, 'tipo_participacion' => 'Principal', 'pagado' => false],
            // Mecánico Hugo Ariel asignado a la orden 502
            ['ci_personal' => '6739154', 'nro_orden_trabajo' => 502, 'id_mano_obra' => 2, 'tipo_participacion' => 'Principal', 'pagado' => true],
        ]);

        // 6. CONSUMO DE REPUESTOS EN LAS ÓRDENES (Métrica 3 - Detalle Repuesto)
        DB::table('detalle_repuesto')->insertOrIgnore([
            ['nro_orden_trabajo' => 501, 'id_repuesto' => 2, 'cantidad' => 2, 'precio_unitario' => 15.00, 'descuento' => 0.00], // 2 filtros
            ['nro_orden_trabajo' => 502, 'id_repuesto' => 1, 'cantidad' => 1, 'precio_unitario' => 45.00, 'descuento' => 5.00], // 1 pastilla
        ]);

        // 7. FACTURAS Y COBRO DE CUOTAS DE HOY (Métrica 4 - Ingresos)
        DB::table('factura')->insertOrIgnore([
            ['nro' => 801, 'nro_orden_trabajo' => 502, 'fecha_emision' => $hoy, 'nit' => '7234891', 'nombre' => 'Carlos Romero', 'total' => 125.00, 'plazo' => $hoyFecha]
        ]);

        DB::table('cuota')->insertOrIgnore([
            ['nro_factura' => 801, 'nro' => 1, 'monto' => 75.00, 'fecha' => $hoyFecha, 'tipo_pago' => 'efectivo', 'referencia_stripe' => null],
            ['nro_factura' => 801, 'nro' => 2, 'monto' => 50.00, 'fecha' => $hoyFecha, 'tipo_pago' => 'tarjeta', 'referencia_stripe' => null],
        ]);
    }
}