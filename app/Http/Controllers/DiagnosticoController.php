<?php

namespace App\Http\Controllers;

use App\Models\Diagnostico;
use App\Models\DetalleDiagnostico;
use App\Models\OrdenTrabajo;
use App\Models\Bitacora;
use Illuminate\Http\Request;

class DiagnosticoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permiso:CU05_ADD');
    }

    public function create(Request $request)
    {
        $ordenId = $request->query('orden_id');
        $orden = OrdenTrabajo::with('auto')->findOrFail($ordenId);

        return view('diagnostico.create', compact('orden'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'orden_id' => ['required', 'integer', 'exists:orden_trabajo,nro'],
            'fallas' => ['required', 'array', 'min:1'],
            'fallas.*' => ['required', 'string', 'max:1000'],
            'resultado_general' => ['required', 'string', 'max:1200'],
        ]);

        $orden = OrdenTrabajo::findOrFail($request->orden_id);
        $ciPersonal = auth()->user()->persona?->ci;

        if (!$ciPersonal) {
            return back()->with('error', 'No se encontró la cédula del personal que realiza el diagnóstico.');
        }

        $diagnostico = Diagnostico::create([
            'fecha' => now(),
            'ci_personal' => $ciPersonal,
            'placa_auto' => $orden->placa_auto,
        ]);

        $detalles = [];
        foreach (array_values($request->fallas) as $index => $descripcion) {
            $detalles[] = [
                'id_diagnostico' => $diagnostico->id,
                'id_detalle_diagnostico' => $index + 1,
                'descripcion' => $descripcion,
            ];
        }
        DetalleDiagnostico::insert($detalles);

        $orden->update([
            'estado' => 'Diagnóstico Finalizado',
            'observacion_salida' => $request->resultado_general,
        ]);

        Bitacora::registrar('Diagnóstico Técnico', "Diagnóstico #{$diagnostico->id} para orden #{$orden->nro}");

        return redirect()->route('autos.show', $orden->placa_auto)
                         ->with('success', 'Diagnóstico finalizado correctamente.');
    }
}
