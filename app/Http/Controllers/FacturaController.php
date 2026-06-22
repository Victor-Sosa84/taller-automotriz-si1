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

    public function buscarFacturaPorNumero(int $nro)
    {
        $factura = Factura::with(['detalles', 'ordenTrabajo.auto', 'cuotas'])->find($nro);

        if (!$factura) {
            return null;
        }

        return [
            'nro'             => $factura->nro,
            'fecha_emision'   => $factura->fecha_emision->format('Y-m-d'),
            'cliente'         => $factura->nombre,
            'nit'             => $factura->nit,
            'total'           => $factura->total,
            'saldo_pendiente' => $factura->saldo_pendiente,
            'placa_auto'      => $factura->ordenTrabajo?->auto?->placa,
            'detalles'        => $factura->detalles->map(fn ($d) => [
                'descripcion' => $d->descripcion,
                'tipo'        => $d->tipo,
                'cantidad'    => $d->cantidad,
                'precio'      => $d->precio,
            ]),
        ];
    }

    public function buscarFacturasPorPeriodo(?string $desde = null, ?string $hasta = null)
    {
        $query = Factura::with('cuotas')->latest('fecha_emision');

        if ($desde) {
            $query->whereDate('fecha_emision', '>=', $desde);
        }
        if ($hasta) {
            $query->whereDate('fecha_emision', '<=', $hasta);
        }

        $facturas = $query->limit(50)->get();

        return [
            'total_facturado' => $facturas->sum('total'),
            'cantidad'        => $facturas->count(),
            'facturas'        => $facturas->map(fn ($f) => [
                'nro'             => $f->nro,
                'fecha_emision'   => $f->fecha_emision->format('Y-m-d'),
                'total'           => $f->total,
                'saldo_pendiente' => $f->saldo_pendiente,
            ]),
        ];
    }

    public function pdf(Factura $factura)
    {
        $factura->load('detalles', 'ordenTrabajo.auto');
        $pdf = Pdf::loadView('factura.pdf', compact('factura'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download("factura-{$factura->nro}.pdf");
    }
}