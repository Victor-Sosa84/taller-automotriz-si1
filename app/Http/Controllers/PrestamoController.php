<?php
namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\DetallePrestamo;
use App\Models\Herramienta;
use App\Models\PrestamoHerramienta;
use Illuminate\Http\Request;

class PrestamoController extends Controller
{
    public function obtenerPrestamos()
    {
        $prestamos    = PrestamoHerramienta::with(['detalles.herramienta'])->orderBy('id', 'desc')->get();
        $herramientas = Herramienta::where('disponible', true)->orderBy('nro')->get();
        return view('prestamo.index', compact('prestamos', 'herramientas'));
    }

    public function registrarPrestamos(Request $request)
    {
        $request->validate([
            'nro_herramienta' => ['required', 'integer', 'exists:herramienta,nro'],
            'estado_salida'   => ['nullable', 'string', 'max:50'],
            'fecha_salida'    => ['required', 'date'],
        ]);

        $herramienta = Herramienta::findOrFail($request->nro_herramienta);

        if (!$herramienta->disponible) {
            return back()->with('error', 'La herramienta no está disponible.');
        }

        $prestamo = PrestamoHerramienta::create([
            'fecha_salida'    => $request->fecha_salida,
            'fecha_devolucion' => null,
        ]);

        DetallePrestamo::create([
            'id_prestamo_herramienta' => $prestamo->id,
            'nro_herramienta'         => $request->nro_herramienta,
            'estado_salida'           => $herramienta->estado,
            'estado_retorno'          => null,
        ]);

        $herramienta->update(['disponible' => false]);

        Bitacora::registrar('Registrar Préstamo', "Préstamo #{$prestamo->id} - Herramienta #{$request->nro_herramienta}");

        return redirect()->route('prestamo.index')->with('success', 'Préstamo registrado correctamente.');
    }

    public function eliminarPrestamo(int $id)
    {
        $prestamo = PrestamoHerramienta::with('detalles')->findOrFail($id);

        foreach ($prestamo->detalles as $detalle) {
            Herramienta::where('nro', $detalle->nro_herramienta)
                ->update(['disponible' => true]);
        }

        $prestamo->delete();

        Bitacora::registrar('Eliminar Préstamo', "Préstamo #{$id} eliminado.");

        return redirect()->route('prestamo.index')->with('success', 'Préstamo eliminado correctamente.');
    }
}