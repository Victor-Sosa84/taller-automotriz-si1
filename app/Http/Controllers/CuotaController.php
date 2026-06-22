<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Cuota;
use App\Models\Factura;
use Illuminate\Http\Request;

class CuotaController extends Controller
{
    public function buscarCuotasPendientes(?string $desde = null, ?string $hasta = null)
    {
        $query = Factura::with('cuotas')->latest('fecha_emision');

        if ($desde) {
            $query->whereDate('fecha_emision', '>=', $desde);
        }
        if ($hasta) {
            $query->whereDate('fecha_emision', '<=', $hasta);
        }

        $pendientes = $query->get()->filter(fn ($f) => $f->saldo_pendiente > 0);

        return [
            'total_pendiente' => $pendientes->sum('saldo_pendiente'),
            'cantidad'        => $pendientes->count(),
            'facturas'        => $pendientes->values()->map(fn ($f) => [
                'nro_factura'     => $f->nro,
                'fecha_emision'   => $f->fecha_emision->format('Y-m-d'),
                'total'           => $f->total,
                'saldo_pendiente' => $f->saldo_pendiente,
                'plazo'           => $f->plazo?->format('Y-m-d'),
            ]),
        ];
    }

    public function mostrarPago(int $nro)
    {
        $factura = Factura::with('cuotas')->findOrFail($nro);

        return view('cuota.create', compact('factura'));
    }

    public function registrarPago(Request $request, int $nro)
    {
        $factura = Factura::with('cuotas')->findOrFail($nro);

        $request->validate([
            'monto'     => ['required', 'numeric', 'min:0.01', 'max:' . $factura->saldo_pendiente],
            'tipo_pago' => ['required', 'string', 'in:efectivo,tarjeta'],
        ], [
            'monto.max' => 'El monto no puede ser mayor al saldo pendiente (Bs. ' . number_format($factura->saldo_pendiente, 2) . ').',
        ]);

        if ($request->tipo_pago === 'tarjeta') {
            // crearIntentoPago() — integración con Stripe, pendiente de implementar
        }

        Cuota::create([
            'nro_factura'       => $factura->nro,
            'nro'               => Cuota::siguienteNumero($factura->nro),
            'monto'             => $request->monto,
            'fecha'             => now(),
            'tipo_pago'         => $request->tipo_pago,
            'referencia_stripe' => $request->referencia_stripe,
        ]);

        Bitacora::registrar('Registrar Pago', "Factura #{$factura->nro} - Monto: Bs. {$request->monto}");

        return redirect()->route('factura.show', $factura->nro)
                        ->with('success', 'Pago registrado correctamente.');
    }

    public function crearIntentoPago(Request $request)
    {
        $request->validate([
            'monto' => ['required', 'numeric', 'min:0.01'],
        ]);

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $intent = \Stripe\PaymentIntent::create([
            'amount' => (int) round($request->monto * 100),
            'currency' => 'usd',
            'payment_method_types' => ['card'],
        ]);

        return response()->json([
            'client_secret' => $intent->client_secret,
        ]);
    }
}