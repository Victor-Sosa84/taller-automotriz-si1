<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermisoController extends Controller
{
    /**
     * Vista principal — muestra los 3 roles con sus permisos agrupados por módulo.
     * Solo accesible por el administrador.
     */
    public function index()
    {
        $roles    = Rol::with('permisos')->get();
        $permisos = Permiso::orderBy('modulo')->orderBy('etiqueta')->get()
                           ->groupBy('modulo');

        return view('permisos.index', compact('roles', 'permisos'));
    }

    /**
     * Activar o desactivar un permiso para un rol.
     * Recibe: id_rol, id_permiso, estado (Activo | Inactivo)
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'id_rol'     => ['required', 'exists:rol,id'],
            'id_permiso' => ['required', 'exists:permiso,id'],
            'estado'     => ['required', 'in:Activo,Inactivo'],
        ]);

        $rol     = Rol::findOrFail($request->id_rol);
        $permiso = Permiso::findOrFail($request->id_permiso);

        // Verificar si ya existe la relación en rol_permiso
        $existente = DB::table('rol_permiso')
            ->where('id_rol', $rol->id)
            ->where('id_permiso', $permiso->id)
            ->first();

        if ($existente) {
            // Actualizar estado
            DB::table('rol_permiso')
                ->where('id_rol', $rol->id)
                ->where('id_permiso', $permiso->id)
                ->update(['estado' => $request->estado]);
        } else {
            // Crear la relación
            DB::table('rol_permiso')->insert([
                'id_rol'         => $rol->id,
                'id_permiso'     => $permiso->id,
                'estado'         => $request->estado,
                'fecha_registro' => now()->toDateString(),
                'observaciones'  => null,
            ]);
        }

        $accionTexto = $request->estado === 'Activo' ? 'activó' : 'desactivó';
        Bitacora::registrar(
            'Cambio de Permiso',
            "Se {$accionTexto} «{$permiso->etiqueta}» para rol «{$rol->nombre}»"
        );

        return response()->json([
            'success' => true,
            'estado'  => $request->estado,
            'mensaje' => "Permiso «{$permiso->etiqueta}» {$accionTexto} para «{$rol->nombre}».",
        ]);
    }
}
