<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermiso
{
    /**
     * Uso en rutas: middleware('permiso:CU01_ADD')
     *               middleware('permiso:CU01_ADD,CU01_MOD')  ← requiere CUALQUIERA
     *
     * El usuario id=1 (Admin Principal) bypassa todo sin consultar BD.
     */
    public function handle(Request $request, Closure $next, string ...$permisos): Response
    {
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        // Usuario id=1 tiene acceso total — no consulta rol_permiso
        if ((int) $usuario->id_usuario === 1) {
            return $next($request);
        }

        $rol = $usuario->rol;

        if (!$rol) {
            return $this->sinPermiso($request, 'Tu cuenta no tiene un rol asignado.');
        }

        foreach ($permisos as $permiso) {
            if ($rol->tienePermiso($permiso)) {
                return $next($request);
            }
        }

        return $this->sinPermiso(
            $request,
            'No tienes permiso para realizar esta acción. Contacta al administrador.'
        );
    }

    private function sinPermiso(Request $request, string $mensaje): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => $mensaje], 403);
        }

        return redirect()->route('dashboard')->with('error', $mensaje);
    }
}