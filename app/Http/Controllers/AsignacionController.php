<?php
namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\ManoObra;
use App\Models\OrdenTrabajo;
use App\Models\Persona;
use App\Models\Realiza;
use Illuminate\Http\Request;

class AsignacionController extends Controller
{
    public function obtenerAsignaciones(int $nro)
    {
        $orden = OrdenTrabajo::with(['realiza.persona', 'realiza.manoObra'])->findOrFail($nro);
        $personal = Persona::where('es_personal', true)->get();
        $servicios = ManoObra::orderBy('descripcion')->get();
        return view('asignacion.index', compact('orden', 'personal', 'servicios'));
    }

    public function registrarAsignacion(Request $request, int $nro)
    {
        $orden = OrdenTrabajo::findOrFail($nro);
        if (!$orden->puede_editarse) {
            return redirect()->back()->with('error', 'No se puede asignar responsables a una orden de trabajo finalizada.');
        }

        $request->validate([
            'ci_personal'        => ['required', 'string', 'exists:persona,ci'],
            'id_mano_obra'       => ['required', 'integer', 'exists:mano_obra,id'],
            'tipo_participacion' => ['nullable', 'string', 'max:100'],
        ]);

        Realiza::create([
            'ci_personal'        => $request->ci_personal,
            'nro_orden_trabajo'  => $nro,
            'id_mano_obra'       => $request->id_mano_obra,
            'tipo_participacion' => $request->tipo_participacion,
        ]);

        Bitacora::registrar('Asignar Responsable', "OT #{$nro} - Personal: {$request->ci_personal}");

        return redirect()->route('asignacion.index', $nro)
                        ->with('success', 'Responsable asignado correctamente.');
    }

    public function actualizarAsignacion(Request $request, int $nro, string $ci, int $idManoObra)
    {
        $orden = OrdenTrabajo::findOrFail($nro);
        if (!$orden->puede_editarse) {
            return redirect()->back()->with('error', 'No se puede asignar responsables a una orden de trabajo finalizada.');
        }

        $request->validate([
            'tipo_participacion' => ['nullable', 'string', 'max:100'],
        ]);

        Realiza::where('nro_orden_trabajo', $nro)
                ->where('ci_personal', $ci)
                ->where('id_mano_obra', $idManoObra)
                ->update(['tipo_participacion' => $request->tipo_participacion]);

        Bitacora::registrar('Modificar Asignación', "OT #{$nro} - Personal: {$ci}");

        return redirect()->route('asignacion.index', $nro)
                        ->with('success', 'Asignación actualizada correctamente.');
    }
}