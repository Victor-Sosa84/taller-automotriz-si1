<?php

namespace App\Http\Controllers;

use App\Models\Auto;
use App\Models\Diagnostico;
use Illuminate\Http\Request;

class HistorialController extends Controller
{
    // ── INDEX — búsqueda de vehículo ─────────────────────────────
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = Auto::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('placa',  'like', "%{$search}%")
                ->orWhere('marca',  'like', "%{$search}%")
                ->orWhere('modelo', 'like', "%{$search}%");
            });
        }

        $autos = $query->orderBy('placa')->paginate(15)->withQueryString();

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
            'ordenesPendientes',
        ])->findOrFail($placa);

        return view('historial.show', compact('auto'));
    }
}
