<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\Pago;
use App\Models\Realiza;
use Illuminate\Http\Request;

class PagoController extends Controller
{

    // +listarPagos()
    public function listarPagos()
    {
        // Añadimos orderByDesc para ordenar del ID más alto (más reciente) al más bajo
        $pagos = Pago::with('contrato.personal')->orderByDesc('id')->get();
        
        // O si prefieres ordenar estrictamente por la fecha en que se registró el pago:
        // $pagos = Pago::with('contrato.personal')->orderByDesc('fecha_pago')->get();

        // Cargamos los contratos vigentes para poder liquidar desde la interfaz
        $contratosVigentes = Contrato::with('personal')->where('estado', 'Vigente')->get();

        return view('pagos.index', compact('pagos', 'contratosVigentes'));
    }


    // +calcularPago()
    public function calcularPago($id_contrato)
    {
        $contrato = Contrato::with('modalidadRemuneracion')->findOrFail($id_contrato);
        $montoTotal = 0.00;
        $detallesTrabajo = [];

        // Evaluamos según el Tipo de Remuneración del esquema
        if ($contrato->tipo_remuneracion == 1 || strtolower($contrato->modalidadRemuneracion->descripcion) == 'sueldo fijo') {
            // Caso Sueldo Fijo
            $montoTotal = (float) $contrato->valor;
        } else {
            // Caso Porcentaje: Buscamos en la tabla intermedia 'realiza' las tareas hechas por el personal
            // Solo se cuentan los trabajos que todavía no fueron liquidados (pagado = false),
            // así una orden ya pagada en una liquidación anterior nunca se vuelve a cobrar.
            $trabajosRealizados = Realiza::where('ci_personal', $contrato->ci_personal)
                ->where('pagado', false)
                ->with(['manoObra', 'ordenTrabajo'])
                ->whereHas('ordenTrabajo', function($query) {
                    $query->where('estado', 'Finalizada'); // Solo órdenes concluidas
                })
                ->get();

            foreach ($trabajosRealizados as $registro) {
                // Se calcula el porcentaje asignado en el contrato sobre el costo referencial de la mano de obra
                $subtotalTrabajo = ($registro->manoObra->costo_referencial * ($contrato->valor / 100));
                $montoTotal += $subtotalTrabajo;

                $detallesTrabajo[] = [
                    'orden' => $registro->nro_orden_trabajo,
                    'id_mano_obra' => $registro->id_mano_obra,
                    'servicio' => $registro->manoObra->descripcion,
                    'costo_base' => $registro->manoObra->costo_referencial,
                    'comision_calculada' => $subtotalTrabajo
                ];
            }
        }

        return response()->json([
            'contrato' => $contrato,
            'monto_calculado' => $montoTotal,
            'detalles' => $detallesTrabajo
        ]);
    }

    // +mostrarPago()
    public function mostrarPago(Request $request)
    {
        $request->validate([
            'id_contrato' => 'required|integer|exists:contrato,id',
            'tipo' => 'required|string',
            'metodo' => 'required|string'
        ]);

        // Ejecuta internamente el cálculo antes de guardar definitivamente
        $calculo = json_decode($this->calcularPago($request->id_contrato)->getContent());

        $pago = Pago::create([
            'id_contrato' => $request->id_contrato,
            'fecha_pago' => now(),
            'monto' => $calculo->monto_calculado,
            'tipo' => $request->tipo,
            'metodo' => $request->metodo
        ]);

        // Marca como pagados los trabajos incluidos en este cálculo, para que no
        // se vuelvan a contar ni a cobrar en una liquidación futura del mismo contrato.
        $contrato = Contrato::findOrFail($request->id_contrato);
        foreach ($calculo->detalles as $detalle) {
            Realiza::where('ci_personal', $contrato->ci_personal)
                ->where('nro_orden_trabajo', $detalle->orden)
                ->where('id_mano_obra', $detalle->id_mano_obra)
                ->update(['pagado' => true]);
        }

        // Retornamos los datos estructurados listos para ser renderizados en el documento de firma física
        return redirect()->route('pagos.index')->with([
            'success' => 'Liquidación procesada correctamente.',
            'pago_id' => $pago->id,
            'imprimir' => true
        ]);
    }
}