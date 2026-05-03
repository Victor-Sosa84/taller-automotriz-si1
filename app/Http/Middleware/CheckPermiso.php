<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermiso
{
    /**
     * Uso en rutas:
     *   middleware('permiso:CLI_VIEW')
     *   middleware('permiso:CLI_CREATE,CLI_EDIT')  ← requiere CUALQUIERA de los dos
     *
     * El middleware verifica si el rol del usuario tiene al menos uno
     * de los permisos listados con estado 'Activo' en rol_permiso.
     */
    public function handle(Request $request, Closure $next, string ...$permisos): Response
    {
        $usuario = auth()->user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        $rol = $usuario->rol;

        if (!$rol) {
            return $this->sinPermiso($request, 'Tu cuenta no tiene un rol asignado.');
        }

        // Verifica si tiene al menos uno de los permisos requeridos
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

        return redirect()
            ->route('dashboard')
            ->with('error', $mensaje);
    }
}
