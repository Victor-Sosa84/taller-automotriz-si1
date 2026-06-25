<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Puebla el flujo operativo completo (diagnóstico → proforma → orden de
 * trabajo → asignación → factura → cuota) con 15 vehículos, cada uno
 * detenido en una etapa distinta del proceso — simulando un taller en
 * operación real, no un set de datos donde todo llega limpiamente
 * hasta el final.
 *
 * Los mecánicos asignados en 'realiza' son los mismos 4 que ya existen
 * en PersonalSeeder, para que PagoSeeder pueda calcular comisiones
 * reales sobre este trabajo. Por eso este seeder debe correr ANTES de
 * PagoSeeder y DESPUÉS de PersonalSeeder + DatosPruebaSeeder.
 */
class FlujoOperativoSeeder extends Seeder
{
    // CI de los mecánicos ya existentes (PersonalSeeder)
    private const LUIS    = '4521837';
    private const HUGO    = '6739154';
    private const MARCO   = '8214673';
    private const ESTEBAN = '5390218';

    public function run(): void
    {
        $idDiagnostico = 1;
        $nroProforma   = 1;
        $nroOrden      = 1;
        $nroFactura    = 1;

        // ════════════════════════════════════════════════════════════
        // GRUPO 6: Factura paga completamente (2 vehículos)
        // ════════════════════════════════════════════════════════════
        $grupo6 = [
            ['placa' => '8275JPW', 'ci_cliente' => '8143650', 'mecanico' => self::HUGO,  'mano_obra' => 4, 'nombre' => 'Juan Pablo Gutiérrez Flores', 'tipo_pago' => 'tarjeta',  'nit' => '8143650'],
            ['placa' => '2918DKM', 'ci_cliente' => '6957423', 'mecanico' => self::MARCO, 'mano_obra' => 3, 'nombre' => 'Rosa Elena Medina Chávez',     'tipo_pago' => 'efectivo', 'nit' => '1023845'],
        ];

        foreach ($grupo6 as $v) {
            DB::table('diagnostico')->insertOrIgnore([
                'id'          => $idDiagnostico,
                'fecha'       => now()->subDays(30),
                'ci_personal' => self::LUIS,
                'placa_auto'  => $v['placa'],
                'descripcion' => 'Diagnóstico completado, ciclo finalizado y pagado.',
            ]);
            DB::table('detalle_diagnostico')->insertOrIgnore([
                'id_diagnostico'         => $idDiagnostico,
                'id_detalle_diagnostico' => 1,
                'falla'                  => 'Revisión técnica integral',
            ]);

            DB::table('proforma')->insertOrIgnore([
                'nro'            => $nroProforma,
                'ci_cliente'     => $v['ci_cliente'],
                'id_diagnostico' => $idDiagnostico,
                'fecha'          => now()->subDays(29),
                'total_aprox'    => 230.00,
                'estado'         => 'Aprobada',
                'plazo'          => now()->subDays(15)->toDateString(),
            ]);
            DB::table('proforma_repuesto')->insertOrIgnore([
                'nro_proforma' => $nroProforma, 'id_repuesto' => 3, 'cantidad' => 2, 'precio_unitario' => 25.00, 'descuento' => 0,
            ]);
            DB::table('proforma_servicio')->insertOrIgnore([
                'nro_proforma' => $nroProforma, 'id_mano_obra' => $v['mano_obra'], 'costo' => 120.00, 'estado' => 'Realizado', 'cantidad' => 1,
            ]);

            DB::table('orden_trabajo')->insertOrIgnore([
                'nro'                 => $nroOrden,
                'nro_proforma'        => $nroProforma,
                'placa_auto'          => $v['placa'],
                'fecha_inicio'        => now()->subDays(28),
                'fecha_fin'           => now()->subDays(26),
                'estado'              => 'Finalizada',
                'kilometraje'         => 71000,
                'observacion_entrada' => 'Revisión técnica integral solicitada antes de viaje.',
                'observacion_salida'  => 'Vehículo en condiciones óptimas, entregado al cliente.',
            ]);
            DB::table('realiza')->insertOrIgnore([
                'ci_personal'        => $v['mecanico'],
                'nro_orden_trabajo'  => $nroOrden,
                'id_mano_obra'       => $v['mano_obra'],
                'tipo_participacion' => 'Principal',
                'pagado'             => false,
            ]);
            DB::table('detalle_trabajo')->insertOrIgnore([
                'nro_orden_trabajo' => $nroOrden, 'id_mano_obra' => $v['mano_obra'], 'costo' => 120.00, 'estado' => 'Realizado', 'cantidad' => 1,
            ]);

            DB::table('factura')->insertOrIgnore([
                'nro'               => $nroFactura,
                'nro_orden_trabajo' => $nroOrden,
                'fecha_emision'     => now()->subDays(25),
                'nit'               => $v['nit'],
                'nombre'            => $v['nombre'],
                'total'             => 170.00,
                'plazo'             => now()->subDays(10)->toDateString(),
            ]);
            DB::table('detalle_factura')->insertOrIgnore([
                'id' => 1, 'nro_factura' => $nroFactura, 'descripcion' => 'Bujías (x2)', 'tipo' => 'Repuesto', 'cantidad' => 2, 'precio' => 50.00, 'precio_unitario' => 25.00, 'descuento' => 0,
            ]);
            DB::table('detalle_factura')->insertOrIgnore([
                'id' => 2, 'nro_factura' => $nroFactura, 'descripcion' => 'Mano de obra', 'tipo' => 'Servicio', 'cantidad' => 1, 'precio' => 120.00, 'precio_unitario' => 120.00, 'descuento' => 0,
            ]);

            DB::table('cuota')->insertOrIgnore([
                'nro_factura'       => $nroFactura,
                'nro'               => 1,
                'monto'             => 170.00,
                'fecha'             => now()->subDays(24)->toDateString(),
                'tipo_pago'         => $v['tipo_pago'],
                'referencia_stripe' => null,
            ]);

            $idDiagnostico++;
            $nroProforma++;
            $nroOrden++;
            $nroFactura++;
        }

        // ════════════════════════════════════════════════════════════
        // GRUPO 5: Factura generada, cuota pendiente (2 vehículos)
        // ════════════════════════════════════════════════════════════
        $grupo5 = [
            ['placa' => '7091BNF', 'ci_cliente' => '7234891', 'mecanico' => self::MARCO,   'mano_obra' => 1, 'nombre' => 'Carlos Alberto Romero Vaca',  'nit' => '7234891'],
            ['placa' => '4430CVL', 'ci_cliente' => '5812374', 'mecanico' => self::ESTEBAN, 'mano_obra' => 2, 'nombre' => 'María Alejandra Suárez Peña', 'nit' => '2087364'],
        ];

        foreach ($grupo5 as $v) {
            DB::table('diagnostico')->insertOrIgnore([
                'id'          => $idDiagnostico,
                'fecha'       => now()->subDays(20),
                'ci_personal' => self::LUIS,
                'placa_auto'  => $v['placa'],
                'descripcion' => 'Diagnóstico completado, trabajo facturado.',
            ]);
            DB::table('detalle_diagnostico')->insertOrIgnore([
                'id_diagnostico'         => $idDiagnostico,
                'id_detalle_diagnostico' => 1,
                'falla'                  => 'Mantenimiento preventivo programado',
            ]);

            DB::table('proforma')->insertOrIgnore([
                'nro'            => $nroProforma,
                'ci_cliente'     => $v['ci_cliente'],
                'id_diagnostico' => $idDiagnostico,
                'fecha'          => now()->subDays(19),
                'total_aprox'    => 155.00,
                'estado'         => 'Aprobada',
                'plazo'          => now()->subDays(5)->toDateString(),
            ]);
            DB::table('proforma_repuesto')->insertOrIgnore([
                'nro_proforma' => $nroProforma, 'id_repuesto' => 1, 'cantidad' => 1, 'precio_unitario' => 35.00, 'descuento' => 0,
            ]);
            DB::table('proforma_servicio')->insertOrIgnore([
                'nro_proforma' => $nroProforma, 'id_mano_obra' => $v['mano_obra'], 'costo' => 80.00, 'estado' => 'Realizado', 'cantidad' => 1,
            ]);

            DB::table('orden_trabajo')->insertOrIgnore([
                'nro'                 => $nroOrden,
                'nro_proforma'        => $nroProforma,
                'placa_auto'          => $v['placa'],
                'fecha_inicio'        => now()->subDays(18),
                'fecha_fin'           => now()->subDays(16),
                'estado'              => 'Finalizada',
                'kilometraje'         => 38000,
                'observacion_entrada' => 'Mantenimiento preventivo solicitado por el cliente.',
                'observacion_salida'  => 'Servicio completado conforme a lo cotizado.',
            ]);
            DB::table('realiza')->insertOrIgnore([
                'ci_personal'        => $v['mecanico'],
                'nro_orden_trabajo'  => $nroOrden,
                'id_mano_obra'       => $v['mano_obra'],
                'tipo_participacion' => 'Principal',
                'pagado'             => false,
            ]);
            DB::table('detalle_trabajo')->insertOrIgnore([
                'nro_orden_trabajo' => $nroOrden, 'id_mano_obra' => $v['mano_obra'], 'costo' => 80.00, 'estado' => 'Realizado', 'cantidad' => 1,
            ]);

            DB::table('factura')->insertOrIgnore([
                'nro'               => $nroFactura,
                'nro_orden_trabajo' => $nroOrden,
                'fecha_emision'     => now()->subDays(15),
                'nit'               => $v['nit'],
                'nombre'            => $v['nombre'],
                'total'             => 115.00,
                'plazo'             => now()->addDays(15)->toDateString(),
            ]);
            DB::table('detalle_factura')->insertOrIgnore([
                'id' => 1, 'nro_factura' => $nroFactura, 'descripcion' => 'Filtro de aceite', 'tipo' => 'Repuesto', 'cantidad' => 1, 'precio' => 35.00, 'precio_unitario' => 35.00, 'descuento' => 0,
            ]);
            DB::table('detalle_factura')->insertOrIgnore([
                'id' => 2, 'nro_factura' => $nroFactura, 'descripcion' => 'Revisión de frenos', 'tipo' => 'Servicio', 'cantidad' => 1, 'precio' => 80.00, 'precio_unitario' => 80.00, 'descuento' => 0,
            ]);
            // Sin cuota registrada: queda pendiente de pago en su totalidad.

            $idDiagnostico++;
            $nroProforma++;
            $nroOrden++;
            $nroFactura++;
        }

        // GRUPO 4: OT Finalizada, sin factura todavía (3 vehículos)
        // ════════════════════════════════════════════════════════════
        $grupo4 = [
            ['placa' => '2657XQP', 'ci_cliente' => '9341287', 'falla' => 'Correa de distribución rota', 'mecanico' => self::MARCO,   'mano_obra' => 3],
            ['placa' => '3847GKT', 'ci_cliente' => '6957423', 'falla' => 'Frenos gastados por completo', 'mecanico' => self::ESTEBAN, 'mano_obra' => 2],
            ['placa' => '1562MRZ', 'ci_cliente' => '8143650', 'falla' => 'Falla eléctrica intermitente', 'mecanico' => self::HUGO,    'mano_obra' => 4],
        ];

        foreach ($grupo4 as $v) {
            DB::table('diagnostico')->insertOrIgnore([
                'id'          => $idDiagnostico,
                'fecha'       => now()->subDays(12),
                'ci_personal' => self::LUIS,
                'placa_auto'  => $v['placa'],
                'descripcion' => 'Diagnóstico completado, trabajo realizado.',
            ]);
            DB::table('detalle_diagnostico')->insertOrIgnore([
                'id_diagnostico'         => $idDiagnostico,
                'id_detalle_diagnostico' => 1,
                'falla'                  => $v['falla'],
            ]);

            DB::table('proforma')->insertOrIgnore([
                'nro'            => $nroProforma,
                'ci_cliente'     => $v['ci_cliente'],
                'id_diagnostico' => $idDiagnostico,
                'fecha'          => now()->subDays(11),
                'total_aprox'    => 205.00,
                'estado'         => 'Aprobada',
                'plazo'          => now()->addDays(5)->toDateString(),
            ]);
            DB::table('proforma_repuesto')->insertOrIgnore([
                'nro_proforma' => $nroProforma, 'id_repuesto' => 4, 'cantidad' => 1, 'precio_unitario' => 85.00, 'descuento' => 0,
            ]);
            DB::table('proforma_servicio')->insertOrIgnore([
                'nro_proforma' => $nroProforma, 'id_mano_obra' => $v['mano_obra'], 'costo' => 120.00, 'estado' => 'Realizado', 'cantidad' => 1,
            ]);

            DB::table('orden_trabajo')->insertOrIgnore([
                'nro'                 => $nroOrden,
                'nro_proforma'        => $nroProforma,
                'placa_auto'          => $v['placa'],
                'fecha_inicio'        => now()->subDays(10),
                'fecha_fin'           => now()->subDays(8),
                'estado'              => 'Finalizada',
                'kilometraje'         => 62000,
                'observacion_entrada' => 'Vehículo recibido con falla reportada por el cliente.',
                'observacion_salida'  => 'Reparación completada y verificada.',
            ]);
            DB::table('realiza')->insertOrIgnore([
                'ci_personal'        => $v['mecanico'],
                'nro_orden_trabajo'  => $nroOrden,
                'id_mano_obra'       => $v['mano_obra'],
                'tipo_participacion' => 'Principal',
                'pagado'             => false,
            ]);
            DB::table('detalle_trabajo')->insertOrIgnore([
                'nro_orden_trabajo' => $nroOrden, 'id_mano_obra' => $v['mano_obra'], 'costo' => 120.00, 'estado' => 'Realizado', 'cantidad' => 1,
            ]);

            $idDiagnostico++;
            $nroProforma++;
            $nroOrden++;
        }

        // ════════════════════════════════════════════════════════════
        // GRUPO 3: distintos puntos de la cadena diagnóstico→proforma→OT (4 vehículos)
        // La OT se crea al registrar la unidad (Pendiente de Diagnóstico,
        // sin proforma todavía). Tras el diagnóstico pasa a Diagnóstico
        // Finalizado (sigue sin proforma hasta que se guarda una). La
        // proforma nace en Borrador al guardarse, vinculándose a la OT en
        // ese momento — pero la OT no cambia de estado hasta que la
        // proforma se aprueba (recién ahí pasa a En Proceso).
        // ════════════════════════════════════════════════════════════

        // 5841RZD: OT recién creada, sin diagnóstico realizado ni proforma.
        DB::table('orden_trabajo')->insertOrIgnore([
            'nro'                 => $nroOrden,
            'nro_proforma'        => null,
            'placa_auto'          => '5841RZD',
            'fecha_inicio'        => now()->subDay(),
            'fecha_fin'           => null,
            'estado'              => 'Pendiente de Diagnóstico',
            'kilometraje'         => 45000,
            'observacion_entrada' => 'Vehículo recibido, pendiente de diagnóstico.',
            'observacion_salida'  => null,
        ]);
        $nroOrden++;

        // 9263TGM: diagnóstico hecho, proforma recién guardada en Borrador
        // (vinculada a la OT, pero la OT sigue en Diagnóstico Finalizado).
        DB::table('diagnostico')->insertOrIgnore([
            'id'          => $idDiagnostico,
            'fecha'       => now()->subDays(3),
            'ci_personal' => self::LUIS,
            'placa_auto'  => '9263TGM',
            'descripcion' => 'Diagnóstico completado, cotización en elaboración.',
        ]);
        DB::table('detalle_diagnostico')->insertOrIgnore([
            'id_diagnostico' => $idDiagnostico, 'id_detalle_diagnostico' => 1, 'falla' => 'Vibración al frenar',
        ]);
        DB::table('proforma')->insertOrIgnore([
            'nro' => $nroProforma, 'ci_cliente' => '6182054', 'id_diagnostico' => $idDiagnostico,
            'fecha' => now()->subDays(2), 'total_aprox' => 150.00, 'estado' => 'Borrador',
            'plazo' => null,
        ]);
        DB::table('proforma_repuesto')->insertOrIgnore([
            'nro_proforma' => $nroProforma, 'id_repuesto' => 1, 'cantidad' => 1, 'precio_unitario' => 35.00, 'descuento' => 0,
        ]);
        DB::table('proforma_servicio')->insertOrIgnore([
            'nro_proforma' => $nroProforma, 'id_mano_obra' => 1, 'costo' => 50.00, 'estado' => 'Pendiente', 'cantidad' => 1,
        ]);
        DB::table('orden_trabajo')->insertOrIgnore([
            'nro'                 => $nroOrden,
            'nro_proforma'        => $nroProforma,
            'placa_auto'          => '9263TGM',
            'fecha_inicio'        => now()->subDays(3),
            'fecha_fin'           => null,
            'estado'              => 'Diagnóstico Finalizado',
            'kilometraje'         => 38000,
            'observacion_entrada' => 'Vehículo recibido, diagnóstico realizado.',
            'observacion_salida'  => null,
        ]);
        $idDiagnostico++;
        $nroProforma++;
        $nroOrden++;

        // 1738VKL y 6094WBN: proforma ya aprobada, OT en En Proceso.
        $enProceso = [
            ['placa' => '1738VKL', 'ci_cliente' => '7234891', 'falla' => 'Bujías desgastadas',          'mecanico' => self::ESTEBAN],
            ['placa' => '6094WBN', 'ci_cliente' => '5812374', 'falla' => 'Correa de distribución floja', 'mecanico' => self::HUGO],
        ];

        foreach ($enProceso as $v) {
            DB::table('diagnostico')->insertOrIgnore([
                'id'          => $idDiagnostico,
                'fecha'       => now()->subDays(7),
                'ci_personal' => self::LUIS,
                'placa_auto'  => $v['placa'],
                'descripcion' => 'Diagnóstico completado, proforma aprobada por el cliente.',
            ]);
            DB::table('detalle_diagnostico')->insertOrIgnore([
                'id_diagnostico'         => $idDiagnostico,
                'id_detalle_diagnostico' => 1,
                'falla'                  => $v['falla'],
            ]);

            DB::table('proforma')->insertOrIgnore([
                'nro'            => $nroProforma,
                'ci_cliente'     => $v['ci_cliente'],
                'id_diagnostico' => $idDiagnostico,
                'fecha'          => now()->subDays(6),
                'total_aprox'    => 150.00,
                'estado'         => 'Aprobada',
                'plazo'          => now()->addDays(10)->toDateString(),
            ]);
            DB::table('proforma_repuesto')->insertOrIgnore([
                'nro_proforma' => $nroProforma, 'id_repuesto' => 1, 'cantidad' => 1, 'precio_unitario' => 35.00, 'descuento' => 0,
            ]);
            DB::table('proforma_servicio')->insertOrIgnore([
                'nro_proforma' => $nroProforma, 'id_mano_obra' => 1, 'costo' => 50.00, 'estado' => 'Pendiente', 'cantidad' => 1,
            ]);

            DB::table('orden_trabajo')->insertOrIgnore([
                'nro'                 => $nroOrden,
                'nro_proforma'        => $nroProforma,
                'placa_auto'          => $v['placa'],
                'fecha_inicio'        => now()->subDays(3),
                'fecha_fin'           => null,
                'estado'              => 'En Proceso',
                'kilometraje'         => 45000,
                'observacion_entrada' => 'Vehículo recibido en buen estado general.',
                'observacion_salida'  => null,
            ]);
            DB::table('realiza')->insertOrIgnore([
                'ci_personal'        => $v['mecanico'],
                'nro_orden_trabajo'  => $nroOrden,
                'id_mano_obra'       => 1,
                'tipo_participacion' => 'Principal',
                'pagado'             => false,
            ]);

            $idDiagnostico++;
            $nroProforma++;
            $nroOrden++;
        }

        // ════════════════════════════════════════════════════════════
        // GRUPO 2: Proforma emitida, sin aprobar (2 vehículos)
        // ════════════════════════════════════════════════════════════
        $grupo2 = [
            ['placa' => '8506NXC', 'ci_cliente' => '8364791', 'falla' => 'Frenos chillan al frenar'],
            ['placa' => '3729HWP', 'ci_cliente' => '5928473', 'falla' => 'Luz de motor encendida'],
        ];

        foreach ($grupo2 as $v) {
            DB::table('diagnostico')->insertOrIgnore([
                'id'          => $idDiagnostico,
                'fecha'       => now()->subDays(5),
                'ci_personal' => self::LUIS,
                'placa_auto'  => $v['placa'],
                'descripcion' => 'Diagnóstico completado, cotización emitida.',
            ]);
            DB::table('detalle_diagnostico')->insertOrIgnore([
                'id_diagnostico'         => $idDiagnostico,
                'id_detalle_diagnostico' => 1,
                'falla'                  => $v['falla'],
            ]);

            DB::table('proforma')->insertOrIgnore([
                'nro'            => $nroProforma,
                'ci_cliente'     => $v['ci_cliente'],
                'id_diagnostico' => $idDiagnostico,
                'fecha'          => now()->subDays(4),
                'total_aprox'    => 205.00,
                'estado'         => 'Emitida',
                'plazo'          => now()->addDays(10)->toDateString(),
            ]);
            DB::table('proforma_repuesto')->insertOrIgnore([
                'nro_proforma' => $nroProforma, 'id_repuesto' => 2, 'cantidad' => 1, 'precio_unitario' => 120.00, 'descuento' => 0,
            ]);
            DB::table('proforma_servicio')->insertOrIgnore([
                'nro_proforma' => $nroProforma, 'id_mano_obra' => 2, 'costo' => 80.00, 'estado' => 'Pendiente', 'cantidad' => 1,
            ]);

            $idDiagnostico++;
            $nroProforma++;
        }

        // ════════════════════════════════════════════════════════════
        // ════════════════════════════════════════════════════════════
        // GRUPO 1: Solo diagnóstico, sin proforma todavía (2 vehículos)
        // ════════════════════════════════════════════════════════════
        $grupo1 = [
            ['placa' => '6053FRT', 'ci_cliente' => '9582607', 'falla' => 'Ruido en suspensión delantera'],
            ['placa' => '4172LQB', 'ci_cliente' => '6045318', 'falla' => 'Pérdida de potencia al acelerar'],
        ];

        foreach ($grupo1 as $v) {
            DB::table('diagnostico')->insertOrIgnore([
                'id'          => $idDiagnostico,
                'fecha'       => now()->subDays(2),
                'ci_personal' => self::LUIS,
                'placa_auto'  => $v['placa'],
                'descripcion' => 'Revisión preliminar pendiente de cotización.',
            ]);
            DB::table('detalle_diagnostico')->insertOrIgnore([
                'id_diagnostico'         => $idDiagnostico,
                'id_detalle_diagnostico' => 1,
                'falla'                  => $v['falla'],
            ]);
            $idDiagnostico++;
        }
    }
}