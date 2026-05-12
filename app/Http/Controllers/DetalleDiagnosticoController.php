<?php

namespace App\Http\Controllers;

use App\Models\DetalleDiagnostico;
use Illuminate\Http\Request;

class DetalleDiagnosticoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permiso:CU05_ADD');
    }

    public function store(Request $request)
    {
        $request->validate([
            'diagnostico_id' => ['required', 'integer', 'exists:diagnostico,id'],
            'descripcion' => ['required', 'string', 'max:1000'],
        ]);

        $maxDetalle = DetalleDiagnostico::where('id_diagnostico', $request->diagnostico_id)
            ->max('id_detalle_diagnostico');

        DetalleDiagnostico::create([
            'id_diagnostico' => $request->diagnostico_id,
            'id_detalle_diagnostico' => $maxDetalle ? $maxDetalle + 1 : 1,
            'descripcion' => $request->descripcion,
        ]);

        return back()->with('success', 'Detalle de diagnóstico agregado correctamente.');
    }
}
