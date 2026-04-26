<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

use Illuminate\Support\Facades\DB;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    // Deshabilita remember_token — la tabla 'usuario' no tiene esa columna
    public function setRememberToken($value) {}
    public function getRememberToken() { return null; }
    public function getRememberTokenName() { return ''; }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        
        // En AuthenticatedSessionController.php
        DB::table('bitacora')->insert([
            'id_usuario' => Auth::user()->id_usuario, // Guardamos el ID numérico del usuario
            'fecha_hora' => now(),
            'accion'    => 'Inicio de Sesión',
            'ip_equipo'  => request()->ip(), // Laravel detecta la IP automáticamente
        ]);
        
        // return redirect()->intended(route('dashboard', absolute: false));
        return redirect()->route('dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // 1. Guardamos quién está cerrando sesión antes de que Laravel lo "olvide" 
        $usuarioId = Auth::user()->id_usuario;
        
        // 2. Registramos en la bitácora
        DB::table('bitacora')->insert([
            'id_usuario' => $usuarioId,
            'fecha_hora' => now(),
            'accion'    => 'Cierre de Sesión',
            'ip_equipo'  => $request->ip(),
        ]);

        // 3. Proceso normal de cierre de sesión de Laravel
    
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
