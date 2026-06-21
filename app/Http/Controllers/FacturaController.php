<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Factura;
use App\Models\OrdenTrabajo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class FacturaController extends Controller
{
    public function verDetalleFactura(int $nro)
    {
        $orden = OrdenTrabajo::with(['detallesRepuesto.repuesto', 'detallesTrabajo.manoObra'])->findOrFail($nro);
        $cliente = $orden->proforma->cliente;

        return view('factura.create', compact('orden', 'cliente'));
    }

    public function crearFactura(Request $request, int $nro)
    {
        $orden = OrdenTrabajo::findOrFail($nro);

        $request->validate([
            'nit'    => ['required', 'string', 'max:20'],
            'nombre' => ['required', 'string', 'max:100'],
        ]);

        if ($orden->detallesRepuesto->isEmpty() && $orden->detallesTrabajo->isEmpty()) {
            return redirect()->back()->with('error', 'No hay conceptos para facturar.');
        }

        $factura = Factura::guardarFactura($orden, $request->nit, $request->nombre);

        $orden->proforma->cliente->actualizarCliente($request->nit);

        Bitacora::registrar('Generar Factura Final', "Factura #{$factura->nro} - Orden #{$nro}");

        return redirect()->route('factura.show', $factura->nro)
                        ->with('success', 'Factura generada correctamente.');
    }

    public function mostrarFactura(int $nro)
    {
        $factura = Factura::with(['detalles', 'ordenTrabajo.auto'])->findOrFail($nro);
        return view('factura.show', compact('factura'));
    }

    public function pdf(Factura $factura)
    {
        $factura->load('detalles', 'ordenTrabajo.auto');
        $pdf = Pdf::loadView('factura.pdf', compact('factura'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download("factura-{$factura->nro}.pdf");
    }
}