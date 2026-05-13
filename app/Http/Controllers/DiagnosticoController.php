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
        return view('diagnostico.create', compact('orden'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'orden_id'         => ['required', 'integer', 'exists:orden_trabajo,nro'],
            'fallas'           => ['required', 'array', 'min:1'],
            'fallas.*'         => ['required', 'string', 'max:1000'],
            'resultado_general'=> ['required', 'string', 'max:1200'],
            'descripcion'      => ['nullable', 'string', 'max:255'],
        ]);

        $orden      = OrdenTrabajo::findOrFail($request->orden_id);
        $ciPersonal = auth()->user()->persona?->ci;

        if (!$ciPersonal) {
            return back()->with('error', 'El usuario no tiene una ficha de personal asociada.');
        }

        $diagnostico = Diagnostico::create([
            'fecha'       => now(),
            'ci_personal' => $ciPersonal,
            'placa_auto'  => $orden->placa_auto,
            'descripcion' => $request->descripcion,
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

        $orden->update([
            'estado'              => 'Diagnóstico Finalizado',
            'observacion_salida'  => $request->resultado_general,
        ]);

        Bitacora::registrar('Diagnóstico Técnico', "Diagnóstico #{$diagnostico->id} para orden #{$orden->nro}");

        return redirect()->route('autos.show', $orden->placa_auto)
                         ->with('success', 'Diagnóstico finalizado correctamente.');
    }
}