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
            'placa_auto'          => strtoupper($request->placa),
        ]);

        Bitacora::registrar('Registro de Unidad', "Orden #{$orden->nro} - Placa: {$request->placa}");

        return redirect()->route('diagnostico.create', ['orden_id' => $orden->nro])
                        ->with('success', 'Unidad registrada. Continúe con el diagnóstico.');
    }

    public function obtenerOrdenes()
    {
        $ordenes = OrdenTrabajo::with(['proforma', 'auto'])
            ->whereHas('proforma', function ($q) {
                $q->where('estado', 'Aprobada');
            })
            ->get();
        return view('orden_trabajo.index', compact('ordenes'));
    }

    public function obtenerOrden(int $nro)
    {
        $orden = OrdenTrabajo::with(['proforma.cliente', 'auto'])->findOrFail($nro);
        return view('orden_trabajo.show', compact('orden'));
    }

    public function actualizarOrden(Request $request, int $nro)
    {
        $orden = OrdenTrabajo::findOrFail($nro);

        if ($orden->estado === 'Finalizada') {
            return redirect()->back()->with('error', 'No se puede modificar una orden de trabajo finalizada.');
        }

        $request->validate([
            'estado'             => ['required', 'string', 'in:Finalizada,Anulada'],
            'observacion_salida' => ['nullable', 'string', 'max:1000'],
            'fecha_fin'          => ['required_if:estado,Finalizada,Anulada', 'date', 'before_or_equal:today', 'after_or_equal:fecha_inicio'],
        ]);

        $orden->update([
            'estado'             => $request->estado,
            'observacion_salida' => $request->observacion_salida,
            'fecha_fin'          => $request->fecha_fin,
        ]);

        Bitacora::registrar('Actualización de Orden de Trabajo', "Orden #{$nro} - Estado: {$request->estado}");

        return redirect()->route('orden_trabajo.show', $nro)
                        ->with('success', 'Orden de trabajo actualizada correctamente.');
    }

    public function editarOrden(int $nro)
    {
        $orden = OrdenTrabajo::with(['proforma.cliente', 'auto'])->findOrFail($nro);

        if ($orden->estado === 'Finalizada') {
            return redirect()->back()->with('error', 'No se puede modificar una orden de trabajo finalizada.');
        }

        return view('orden_trabajo.edit', compact('orden'));
    }
}