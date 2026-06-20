<?php
namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\DetalleRepuesto;
use App\Models\DetalleTrabajo;
use App\Models\ManoObra;
use App\Models\OrdenTrabajo;
use App\Models\Repuesto;
use Illuminate\Http\Request;

class DetalleOTController extends Controller
{
    public function obtenerDetalles(int $nro)
    {
        $orden = OrdenTrabajo::with([
            'detallesRepuesto.repuesto',
            'detallesTrabajo.manoObra'
        ])->findOrFail($nro);
        $repuestos = Repuesto::orderBy('nombre')->get();
        $servicios = ManoObra::orderBy('descripcion')->get();
        return view('detalle_ot.index', compact('orden', 'repuestos', 'servicios'));
    }

    public function registrarDetalles(Request $request, int $nro)
    {
        $orden = OrdenTrabajo::findOrFail($nro);
        if (!$orden->puede_editarse) {
            return redirect()->back()->with('error', 'No se puede modificar una orden de trabajo finalizada.');
        }

        $validated = $request->validate([
            'tipo'            => ['required', 'in:repuesto,mano_obra'],
            'id_repuesto'     => ['nullable', 'integer'],
            'cantidad'        => ['required', 'integer', 'min:1'],
            'precio_unitario' => ['nullable', 'numeric', 'min:0'],
            'descuento'       => ['nullable', 'numeric', 'min:0', 'max:100'],
            'id_mano_obra'    => ['nullable', 'integer'],
            'costo'           => ['nullable', 'numeric', 'min:0'],
            'estado'          => ['nullable', 'string'],
        ]);

        if ($request->tipo === 'repuesto') {
            DetalleRepuesto::create([
                'nro_orden_trabajo' => $nro,
                'id_repuesto'       => $request->id_repuesto,
                'cantidad'          => $request->cantidad,
                'precio_unitario'   => $request->precio_unitario,
                'descuento'         => $request->descuento ?? 0,
            ]);
            Bitacora::registrar('Registrar Repuesto', "OT #{$nro} - Repuesto #{$request->id_repuesto}");
        } else {
            DetalleTrabajo::create([
                'nro_orden_trabajo' => $nro,
                'id_mano_obra'      => $request->id_mano_obra,
                'cantidad'          => $request->cantidad,
                'costo'             => $request->costo,
                'estado'            => $request->estado ?? 'Pendiente',
            ]);
            Bitacora::registrar('Registrar Mano de Obra', "OT #{$nro} - MO #{$request->id_mano_obra}");
        }

        return redirect()->route('detalle_ot.index', $nro)->with('success', 'Detalle registrado correctamente.');
    }

    public function editarDetalles(Request $request, int $nro, string $tipo, int $id)
    {
        $orden = OrdenTrabajo::findOrFail($nro);
        if (!$orden->puede_editarse) {
            return redirect()->back()->with('error', 'No se puede modificar una orden de trabajo finalizada.');
        }

        $request->validate([
            'cantidad'        => ['required', 'integer', 'min:1'],
            'precio_unitario' => ['nullable', 'numeric', 'min:0'],
            'descuento'       => ['nullable', 'numeric', 'min:0', 'max:100'],
            'costo'           => ['nullable', 'numeric', 'min:0'],
            'estado'          => ['nullable', 'string'],
        ]);

        if ($tipo === 'repuesto') {
            DetalleRepuesto::where('nro_orden_trabajo', $nro)
                ->where('id_repuesto', $id)
                ->update([
                    'cantidad'        => $request->cantidad,
                    'precio_unitario' => $request->precio_unitario,
                    'descuento'       => $request->descuento ?? 0,
                ]);
            Bitacora::registrar('Editar Repuesto', "OT #{$nro} - Repuesto #{$id}");
        } else {
            DetalleTrabajo::where('nro_orden_trabajo', $nro)
                ->where('id_mano_obra', $id)
                ->update([
                    'cantidad' => $request->cantidad,
                    'costo'    => $request->costo,
                    'estado'   => $request->estado,
                ]);
            Bitacora::registrar('Editar Mano de Obra', "OT #{$nro} - MO #{$id}");
        }

        return redirect()->route('detalle_ot.index', $nro)->with('success', 'Detalle actualizado correctamente.');
    }

    public function eliminarDetalles(int $nro, string $tipo, int $id)
    {
        $orden = OrdenTrabajo::findOrFail($nro);
        if (!$orden->puede_editarse) {
            return redirect()->back()->with('error', 'No se puede modificar una orden de trabajo finalizada.');
        }

        if ($tipo === 'repuesto') {
            DetalleRepuesto::where('nro_orden_trabajo', $nro)
                ->where('id_repuesto', $id)
                ->delete();
            Bitacora::registrar('Eliminar Repuesto', "OT #{$nro} - Repuesto #{$id}");
        } else {
            DetalleTrabajo::where('nro_orden_trabajo', $nro)
                ->where('id_mano_obra', $id)
                ->delete();
            Bitacora::registrar('Eliminar Mano de Obra', "OT #{$nro} - MO #{$id}");
        }

        return redirect()->route('detalle_ot.index', $nro)->with('success', 'Detalle eliminado correctamente.');
    }
}