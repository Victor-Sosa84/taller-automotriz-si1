<?php
namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\DetallePrestamo;
use App\Models\Herramienta;
use App\Models\PrestamoHerramienta;
use Illuminate\Http\Request;

class HerramientaController extends Controller
{
    public function actualizarEstado(Request $request, int $idPrestamo)
    {
        $request->validate([
            'estado_retorno'   => ['required', 'string', 'max:50'],
            'fecha_devolucion' => ['required', 'date'],
        ]);

        $prestamo = PrestamoHerramienta::with('detalles')->findOrFail($idPrestamo);

        $prestamo->update(['fecha_devolucion' => $request->fecha_devolucion]);

        $detalle = $prestamo->detalles->first();
        $detalle = $prestamo->detalles->first();
        if ($detalle) {
            DetallePrestamo::where('id_prestamo_herramienta', $idPrestamo)
                ->where('nro_herramienta', $detalle->nro_herramienta)
                ->update(['estado_retorno' => $request->estado_retorno]);

            Herramienta::where('nro', $detalle->nro_herramienta)->update([
                'disponible' => true,
                'estado'     => $request->estado_retorno,
            ]);
        }

        Bitacora::registrar('Actualizar Estado Herramienta', "Préstamo #{$idPrestamo} devuelto.");

        return redirect()->route('prestamo.index')->with('success', 'Devolución registrada correctamente.');
    }
}