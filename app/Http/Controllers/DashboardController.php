<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Usuario;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Redirige al dashboard correcto según el rol del usuario autenticado.
     * Esta es la ruta raíz post-login.
     */
    public function index()
    {
        $usuario = auth()->user();

        return match ((int) $usuario->id_rol) {
            1 => redirect()->route('dashboard.admin'),
            2 => redirect()->route('dashboard.mecanico'),
            3 => redirect()->route('dashboard.recepcionista'),
            default => abort(403, 'Rol no reconocido.'),
        };
    }

    // ── Admin ────────────────────────────────────────────────────
    public function admin()
    {
        $totalUsuarios    = Usuario::count();
        $totalPersonal    = \App\Models\Persona::where('es_personal', 1)->count();
        $totalClientes    = \App\Models\Persona::where('es_cliente', 1)->count();
        $ultimasBitacoras = \App\Models\Bitacora::with('usuario')
                                ->orderByDesc('fecha_hora')
                                ->limit(5)
                                ->get();

        return view('dashboard.admin', compact(
            'totalUsuarios',
            'totalPersonal',
            'totalClientes',
            'ultimasBitacoras'
        ));
    }

    // ── Mecánico ─────────────────────────────────────────────────
    public function mecanico()
    {
        // Aquí en el futuro podrás cargar las órdenes de trabajo asignadas
        return view('dashboard.mecanico');
    }

    // ── Recepcionista ────────────────────────────────────────────
    public function recepcionista()
    {
        // Aquí en el futuro podrás cargar proformas, clientes del día, etc.
        return view('dashboard.recepcionista');
    }
}
