<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Dashboard genérico — muestra stats según permisos del usuario.
     * Ya no redirige por rol hardcodeado.
     */
    public function index()
    {
        $usuario = Auth::user();

        $stats = [
            'totalUsuarios' => $usuario->puede('CU13_BUS')
                ? Usuario::count() : null,
            'totalClientes' => $usuario->puede('CU01_BUS')
                ? Persona::where('es_cliente', true)->count() : null,
            'totalPersonal' => $usuario->puede('CU13_BUS')
                ? Persona::where('es_personal', true)->count() : null,
        ];

        $ultimasBitacoras = $usuario->puede('CU21_BUS')
            ? Bitacora::with('usuario')->orderByDesc('fecha_hora')->limit(5)->get()
            : collect();

        return view('dashboard.index', compact('stats', 'ultimasBitacoras'));
    }
}