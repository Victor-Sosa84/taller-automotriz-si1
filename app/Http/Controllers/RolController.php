<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RolController extends Controller
{
    public function index()
    {
        $roles = Rol::withCount('usuarios')->orderBy('id')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => ['required', 'string', 'max:50', 'unique:rol,nombre'],
            'descripcion' => ['nullable', 'string', 'max:150'],
        ]);

        $rol = Rol::create([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        Bitacora::registrar('Creación de Rol/Perfil', "Rol: {$rol->nombre}");

        return redirect()->route('roles.index')
                         ->with('success', "Rol «{$rol->nombre}» creado. Asigna sus privilegios desde la sección Permisos.");
    }

    public function edit(Rol $role)
    {
        // Proteger el rol base (id=1)
        if ($role->id === 1) {
            return redirect()->route('roles.index')
                             ->with('error', 'El rol base del sistema no puede modificarse.');
        }
        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Rol $role)
    {
        if ($role->id === 1) {
            return redirect()->route('roles.index')
                             ->with('error', 'El rol base del sistema no puede modificarse.');
        }

        $request->validate([
            'nombre'      => ['required', 'string', 'max:50', Rule::unique('rol', 'nombre')->ignore($role->id)],
            'descripcion' => ['nullable', 'string', 'max:150'],
        ]);

        $role->update([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        Bitacora::registrar('Edición de Rol/Perfil', "Rol: {$role->nombre}");

        return redirect()->route('roles.index')
                         ->with('success', "Rol «{$role->nombre}» actualizado.");
    }

    public function destroy(Rol $role)
    {
        if ($role->id === 1) {
            return back()->with('error', 'El rol base del sistema no puede eliminarse.');
        }

        if ($role->usuarios()->exists()) {
            return back()->with('error', "No se puede eliminar «{$role->nombre}» — tiene usuarios asignados.");
        }

        $nombre = $role->nombre;
        $role->delete();

        Bitacora::registrar('Eliminación de Rol/Perfil', "Rol: {$nombre}");

        return redirect()->route('roles.index')
                         ->with('success', "Rol «{$nombre}» eliminado.");
    }
}
