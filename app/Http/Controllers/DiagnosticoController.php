<?php
namespace App\Http\Controllers;

use App\Models\Diagnostico;
use App\Models\DetalleDiagnostico;
use App\Models\OrdenTrabajo;
use App\Models\Bitacora;
use Illuminate\Http\Request;

class DiagnosticoController extends Controller
{
    public function create(Request $request)
    {
        $ordenId = $request->query('orden_id');
        $orden   = OrdenTrabajo::with('auto')->findOrFail($ordenId);
        $from    = $request->query('from'); // 'auto', 'historial', o null
        return view('diagnostico.create', compact('orden', 'from'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'orden_id'         => ['required', 'integer', 'exists:orden_trabajo,nro'],
            'fallas'      => ['required', 'array', 'min:1'],
            'fallas.*'    => ['required', 'string', 'max:1000'],
            'descripcion' => ['required', 'string', 'max:1200'],
        ]);

        $orden      = OrdenTrabajo::findOrFail($request->orden_id);
        $ciPersonal = auth()->user()->persona?->ci;

        if (!$ciPersonal) {
            return back()->with('error', 'El usuario no tiene una ficha de personal asociada.');
        }

        $diagnostico = Diagnostico::create([
            'fecha'       => now(),
            'ci_personal' => $ciPersonal,
            'placa_auto' => strtoupper($orden->placa_auto),
            'descripcion' => $request->descripcion,  // aquí va el dictamen completo
        ]);

        $detalles = [];
        foreach (array_values($request->fallas) as $i => $descripcion) {
            $detalles[] = [
                'id_diagnostico'          => $diagnostico->id,
                'id_detalle_diagnostico'  => $i + 1,
                'falla'             => $descripcion,
            ];
        }
        DetalleDiagnostico::insert($detalles);

        $orden->update(['estado' => 'Diagnóstico Finalizado']);

        Bitacora::registrar('Diagnóstico Técnico', "Diagnóstico #{$diagnostico->id} para orden #{$orden->nro}");

        return redirect()->route('diagnostico.show', $diagnostico->id)
                ->with('success', 'Diagnóstico finalizado correctamente.');
    }

    public function show(Diagnostico $diagnostico)
    {
        $diagnostico->load('auto', 'persona', 'detalles', 'proforma.repuestos.repuesto', 'proforma.servicios.manoObra');
        $from = request()->query('from');
        return view('diagnostico.show', compact('diagnostico', 'from'));
    }

    public function buscarDiagnosticosPorPeriodo(?string $desde = null, ?string $hasta = null, ?string $placa = null)
    {
        $query = Diagnostico::with(['auto', 'persona', 'detalles'])->latest('fecha');

        if ($desde) {
            $query->whereDate('fecha', '>=', $desde);
        }
        if ($hasta) {
            $query->whereDate('fecha', '<=', $hasta);
        }
        if ($placa) {
            $query->where('placa_auto', 'like', '%' . $placa . '%');
        }

        $diagnosticos = $query->limit(50)->get();

        return [
            'cantidad'      => $diagnosticos->count(),
            'diagnosticos'  => $diagnosticos->map(fn ($d) => [
                'id'          => $d->id,
                'fecha'       => $d->fecha->format('Y-m-d'),
                'placa_auto'  => $d->placa_auto,
                'tecnico'     => $d->persona?->nombre,
                'descripcion' => $d->descripcion,
                'fallas'      => $d->detalles->pluck('falla'),
            ]),
        ];
    }
}