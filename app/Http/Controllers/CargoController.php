<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\TipoTrabajador;
use App\Models\Usuario;
use Illuminate\Http\Request;

class CargoController extends Controller
{
    // ── INDEX — ver cargos de un usuario ─────────────────────────
    public function index(int $idUsuario)
    {
        $usuario = Usuario::with(['persona.tiposTrabajador', 'rol'])
                          ->findOrFail($idUsuario);

        // Solo tiene sentido gestionar cargos de personal (no clientes puros)
        if (!$usuario->persona || !$usuario->persona->es_personal) {
            return redirect()->route('usuarios.index')
                             ->with('error', 'Este usuario no tiene perfil de personal.');
        }

        $tiposTodos      = TipoTrabajador::orderBy('descripcion')->get();
        $tiposAsignados  = $usuario->persona->tiposTrabajador->pluck('id')->toArray();

        return view('cargos.index', compact('usuario', 'tiposTodos', 'tiposAsignados'));
    }

    // ── STORE — asignar un tipo de trabajo ───────────────────────
    public function store(Request $request, int $idUsuario)
    {
        $request->validate([
            'id_tipo_trabajador' => ['required', 'exists:tipo_trabajador,id'],
        ]);

        $usuario = Usuario::with('persona')->findOrFail($idUsuario);
        $persona = $usuario->persona;

        // Verificar que no esté ya asignado
        $yaAsignado = $persona->tiposTrabajador()
                              ->where('id_tipo_trabajador', $request->id_tipo_trabajador)
                              ->exists();

        if ($yaAsignado) {
            return back()->with('error', 'Este cargo ya está asignado al usuario.');
        }

        $persona->tiposTrabajador()->attach($request->id_tipo_trabajador);

        $tipo = TipoTrabajador::find($request->id_tipo_trabajador);

        return back()->with('success', "Cargo «{$tipo->descripcion}» asignado correctamente.");
    }

    // ── DESTROY — quitar un tipo de trabajo ──────────────────────
    public function destroy(int $idUsuario, int $idTipo)
    {
        $usuario = Usuario::with('persona')->findOrFail($idUsuario);
        $persona = $usuario->persona;

        $tipo = TipoTrabajador::findOrFail($idTipo);

        $persona->tiposTrabajador()->detach($idTipo);

        return back()->with('success', "Cargo «{$tipo->descripcion}» removido.");
    }
}
