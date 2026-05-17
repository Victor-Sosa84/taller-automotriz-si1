<?php
namespace App\Http\Controllers;

use App\Models\Auto;
use App\Models\Bitacora;
use App\Models\OrdenTrabajo;
use Illuminate\Http\Request;

class OrdenTrabajoController extends Controller
{
    public function create(Request $request)
    {
        $placa = $request->query('placa');
        $auto  = $placa ? Auto::find($placa) : null;
        return view('orden_trabajo.create', compact('auto', 'placa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'placa'                   => ['required', 'string', 'max:15', 'exists:auto,placa'],
            'kilometraje'             => ['required', 'integer', 'min:0'],
            'combustible'             => ['required', 'in:Vacío,1/4,1/2,3/4,Lleno'],
            'inventario'              => ['array'],
            'inventario.*'            => ['in:Llanta Auxilio,Gato,Herramientas,Radio'],
            'observaciones_adicionales'=> ['nullable', 'string', 'max:1000'],
        ]);

        $inventario = $request->input('inventario', []);
        $observacion = "Combustible: {$request->combustible}. ";
        $observacion .= 'Inventario: ' . ($inventario ? implode(', ', $inventario) : 'Ninguno') . '.';

        if ($request->filled('observaciones_adicionales')) {
            $observacion .= ' Observaciones: ' . $request->observaciones_adicionales . '.';
        }

        $orden = OrdenTrabajo::create([
            'nro_proforma'        => null,
            'fecha_inicio'        => now(),
            'estado'              => 'Pendiente de Diagnóstico',
            'kilometraje'         => $request->kilometraje,
            'observacion_entrada' => $observacion,
            'observacion_salida'  => null,
            'placa_auto'          => $request->placa,
        ]);

        Bitacora::registrar('Registro de Unidad', "Orden #{$orden->nro} - Placa: {$request->placa}");

        return redirect()->route('diagnostico.create', ['orden_id' => $orden->nro])
                        ->with('success', 'Unidad registrada. Continúe con el diagnóstico.');
    }
}