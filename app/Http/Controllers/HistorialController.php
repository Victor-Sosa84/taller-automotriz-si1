<?php

namespace App\Http\Controllers;

use App\Models\Auto;
use App\Models\Diagnostico;
use Illuminate\Http\Request;

class HistorialController extends Controller
{
    // ── INDEX — búsqueda de vehículo ─────────────────────────────
    // El usuario busca por placa para ver el historial
    public function index(Request $request)
    {
        $autos = collect();
        $search = $request->get('search');

        if ($search) {
            $autos = Auto::where('placa', 'like', "%{$search}%")
                         ->orWhere('marca', 'like', "%{$search}%")
                         ->orWhere('modelo', 'like', "%{$search}%")
                         ->paginate(10)
                         ->withQueryString();
        }

        return view('historial.index', compact('autos', 'search'));
    }

    // ── SHOW — historial completo de un vehículo ─────────────────
    public function show(string $placa)
    {
        $auto = Auto::with([
            'diagnosticos' => function ($q) {
                $q->orderByDesc('fecha');
            },
            'diagnosticos.persona',
            'diagnosticos.detalles',
            'diagnosticos.proforma',
            'diagnosticos.proforma.ordenTrabajo',
            'diagnosticos.proforma.ordenTrabajo.detallesTrabajo.manoObra',
            'diagnosticos.proforma.ordenTrabajo.detallesRepuesto.repuesto',
        ])->findOrFail($placa);

        return view('historial.show', compact('auto'));
    }
}
