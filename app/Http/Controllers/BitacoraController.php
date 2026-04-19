<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    public function index(Request $request)
    {
        $query = Bitacora::with('usuario')->orderByDesc('fecha_hora');

        // Filtro por acción
        if ($accion = $request->get('accion')) {
            $query->where('accion', 'like', "%{$accion}%");
        }

        // Filtro por usuario
        if ($usuario = $request->get('usuario')) {
            $query->whereHas('usuario', fn($q) =>
                $q->where('nombre_usuario', 'like', "%{$usuario}%")
            );
        }

        // Filtro por fecha
        if ($fecha = $request->get('fecha')) {
            $query->whereDate('fecha_hora', $fecha);
        }

        $registros = $query->paginate(20)->withQueryString();

        return view('bitacora.index', compact('registros'));
    }
}
