<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Puebla 365 días hacia atrás con 0 a 8 órdenes de trabajo nuevas por día,
 * respetando la cadena de dependencias FK completa:
 * diagnostico -> detalle_diagnostico -> proforma -> orden_trabajo -> realiza
 * -> detalle_repuesto -> factura -> cuota.
 *
 * Pensado para poder probar los filtros de Día / Semana / Mes / Año del
 * dashboard (CU23) con datos distribuidos de forma realista.
 *
 * IDs con offset alto (90000+) para no chocar con los demás seeders.
 * Usa insertOrIgnore + ejecución idempotente (safe de correr más de una vez).
 */
class DashboardHistoricoSeeder extends Seeder
{
    // CIs de mecánicos ya existentes (PersonalSeeder)
    private const MECANICOS = ['4521837', '6739154', '8214673', '5390218', '7461923', '3957284', '6082417'];

    // CIs de clientes ya existentes (DatosPruebaSeeder)
    private const CLIENTES = [
        '7234891', '5812374', '8143650', '6957423', '9341287', '6428913',
        '8217465', '5739182', '7160924', '9582607', '6045318', '8364791',
        '5928473', '7491036', '6182054',
    ];

    // Catálogos ya sembrados (CatalogoSeeder): repuesto 1-4, mano_obra 1-4
    private const REPUESTOS  = [1, 2, 3, 4];
    private const MANOS_OBRA = [1, 2, 3, 4];

    private const OFFSET = 90000; // punto de partida de IDs para no chocar con otros seeders

    public function run(): void
    {
        $idDiagnostico = self::OFFSET;
        $nroProforma   = self::OFFSET;
        $nroOrden      = self::OFFSET;
        $nroFactura    = self::OFFSET;

        // 365 días hacia atrás, incluyendo hoy
        for ($diasAtras = 365; $diasAtras >= 0; $diasAtras--) {
            $fechaDia = now()->subDays($diasAtras);

            // 0 a 8 órdenes de trabajo nuevas ese día (mínimo 0, máximo 8)
            $cantidadOrdenes = random_int(0, 8);

            for ($i = 0; $i < $cantidadOrdenes; $i++) {
                $cliente  = self::CLIENTES[array_rand(self::CLIENTES)];
                $mecanico = self::MECANICOS[array_rand(self::MECANICOS)];
                $repuesto = self::REPUESTOS[array_rand(self::REPUESTOS)];
                $manoObra = self::MANOS_OBRA[array_rand(self::MANOS_OBRA)];

                // Horas distintas dentro del día para no acumular todo a medianoche
                $horaBase = $fechaDia->copy()->setTime(random_int(8, 17), random_int(0, 59));

                // ── 1. Diagnóstico ──────────────────────────────────────
                DB::table('diagnostico')->insertOrIgnore([
                    'id'          => $idDiagnostico,
                    'fecha'       => $horaBase,
                    'ci_personal' => $mecanico,
                    'placa_auto'  => $this->placaAleatoria(),
                    'descripcion' => 'Diagnóstico generado por seeder histórico del dashboard.',
                ]);
                DB::table('detalle_diagnostico')->insertOrIgnore([
                    'id_diagnostico'         => $idDiagnostico,
                    'id_detalle_diagnostico' => 1,
                    'falla'                  => 'Revisión general programada',
                ]);

                // ── 2. Proforma ──────────────────────────────────────────
                DB::table('proforma')->insertOrIgnore([
                    'nro'            => $nroProforma,
                    'ci_cliente'     => $cliente,
                    'id_diagnostico' => $idDiagnostico,
                    'fecha'          => $horaBase,
                    'total_aprox'    => random_int(80, 400),
                    'estado'         => 'Aprobada',
                    'plazo'          => $horaBase->copy()->addDays(15)->toDateString(),
                ]);

                // ── 3. Orden de trabajo ──────────────────────────────────
                // Si fue hace más de 3 días, ya está Finalizada; si es reciente, puede seguir En Proceso.
                $estadoOrden = $diasAtras > 3 ? 'Finalizada' : (random_int(0, 1) ? 'Finalizada' : 'En Proceso');
                $fechaFin    = $estadoOrden === 'Finalizada' ? $horaBase->copy()->addHours(random_int(2, 6)) : null;

                DB::table('orden_trabajo')->insertOrIgnore([
                    'nro'                  => $nroOrden,
                    'nro_proforma'         => $nroProforma,
                    'fecha_inicio'         => $horaBase,
                    'fecha_fin'            => $fechaFin,
                    'estado'               => $estadoOrden,
                    'kilometraje'          => random_int(10000, 150000),
                    'observacion_entrada'  => 'Ingreso registrado por seeder histórico.',
                    'observacion_salida'   => $estadoOrden === 'Finalizada' ? 'Trabajo concluido sin observaciones.' : null,
                ]);

                // ── 4. Realiza (mecánico asignado) ───────────────────────
                DB::table('realiza')->insertOrIgnore([
                    'ci_personal'        => $mecanico,
                    'nro_orden_trabajo'  => $nroOrden,
                    'id_mano_obra'       => $manoObra,
                    'tipo_participacion' => 'Principal',
                    'pagado'             => $estadoOrden === 'Finalizada',
                ]);

                // ── 5. Detalle de repuesto usado ─────────────────────────
                DB::table('detalle_repuesto')->insertOrIgnore([
                    'nro_orden_trabajo' => $nroOrden,
                    'id_repuesto'       => $repuesto,
                    'cantidad'          => random_int(1, 3),
                    'precio_unitario'   => random_int(15, 120),
                    'descuento'         => 0,
                ]);

                // ── 6. Factura y cuota (solo si la orden está Finalizada) ─
                if ($estadoOrden === 'Finalizada') {
                    $totalFactura = random_int(80, 400);

                    DB::table('factura')->insertOrIgnore([
                        'nro'               => $nroFactura,
                        'nro_orden_trabajo' => $nroOrden,
                        'fecha_emision'     => $fechaFin,
                        'nit'               => $cliente,
                        'nombre'            => 'Cliente Seeder Histórico',
                        'total'             => $totalFactura,
                        'plazo'             => $fechaFin->copy()->addDays(10)->toDateString(),
                    ]);

                    DB::table('cuota')->insertOrIgnore([
                        'nro_factura'        => $nroFactura,
                        'nro'                => 1,
                        'monto'              => $totalFactura,
                        'fecha'              => $fechaFin->toDateString(),
                        'tipo_pago'          => random_int(0, 1) ? 'efectivo' : 'tarjeta',
                        'referencia_stripe'  => null,
                    ]);

                    $nroFactura++;
                }

                $idDiagnostico++;
                $nroProforma++;
                $nroOrden++;
            }
        }
    }

    private function placaAleatoria(): string
    {
        $letras  = chr(random_int(65, 90)) . chr(random_int(65, 90)) . chr(random_int(65, 90));
        $numeros = random_int(1000, 9999);
        return "{$numeros}{$letras}";
    }
}