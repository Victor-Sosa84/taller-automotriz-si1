<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRol
{
    /**
     * Uso en rutas: middleware('rol:1')  o  middleware('rol:1,2')
     * Los números corresponden a id_rol en la tabla rol:
     *   1 = Administrador
     *   2 = Mecánico Jefe
     *   3 = Recepcionista
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $usuario = auth()->user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        if (!in_array((string) $usuario->id_rol, $roles)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
