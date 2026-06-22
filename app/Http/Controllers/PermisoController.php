<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermisoController extends Controller
{
    public function index()
    {
        $roles = Rol::with('permisos')->get();

        $permisos = Permiso::orderBy('paquete')
                        ->orderBy('caso_uso')
                        ->orderBy('id')
                        ->get()
                        ->groupBy('paquete')
                        ->map(fn($grupo) => $grupo->groupBy('caso_uso'));

        return view('permisos.index', compact('roles', 'permisos'));
    }

    // ── Validación común: bloquear cambios en rol id=1 ───────────
    private function validarRol(int $idRol): ?array
    {
        if ($idRol === 1) {
            return ['success' => false, 'mensaje' => 'Los privilegios del rol base del sistema no pueden modificarse.'];
        }
        return null;
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'id_rol'     => ['required', 'exists:rol,id'],
            'id_permiso' => ['required', 'exists:permiso,id'],
            'estado'     => ['required', 'in:Activo,Inactivo'],
        ]);

        // Bloquear modificaciones al rol id=1
        if ((int) $request->id_rol === 1) {
            return response()->json([
                'success' => false,
                'mensaje' => 'El rol base del sistema no puede modificarse.',
            ]);
        }

        $rol     = Rol::findOrFail($request->id_rol);
        $permiso = Permiso::findOrFail($request->id_permiso);

        DB::table('rol_permiso')->updateOrInsert(
            ['id_rol' => $rol->id, 'id_permiso' => $permiso->id],
            ['estado' => $request->estado, 'fecha_registro' => now()->toDateString()]
        );

        $accion = $request->estado === 'Activo' ? 'activó' : 'desactivó';
        Bitacora::registrar(
            'Cambio de Privilegio',
            "Se {$accion} «{$permiso->etiqueta}» ({$permiso->caso_uso}) para rol «{$rol->nombre}»"
        );

        return response()->json([
            'success' => true,
            'estado'  => $request->estado,
            'mensaje' => "Privilegio «{$permiso->etiqueta}» {$accion} para «{$rol->nombre}».",
        ]);
    }

    public function toggleCU(Request $request)
    {
        $request->validate([
            'id_rol'   => ['required', 'exists:rol,id'],
            'caso_uso' => ['required', 'string'],
            'estado'   => ['required', 'in:Activo,Inactivo'],
        ]);

        if ((int) $request->id_rol === 1) {
            return response()->json([
                'success' => false,
                'mensaje' => 'El rol base del sistema no puede modificarse.',
            ]);
        }

        $rol      = Rol::findOrFail($request->id_rol);
        $permisos = Permiso::where('caso_uso', $request->caso_uso)->get();

        foreach ($permisos as $permiso) {
            DB::table('rol_permiso')->updateOrInsert(
                ['id_rol' => $rol->id, 'id_permiso' => $permiso->id],
                ['estado' => $request->estado, 'fecha_registro' => now()->toDateString()]
            );
        }

        $accion = $request->estado === 'Activo' ? 'activaron' : 'desactivaron';
        Bitacora::registrar(
            'Cambio masivo de Privilegios',
            "Se {$accion} todos los privilegios de {$request->caso_uso} para rol «{$rol->nombre}»"
        );

        return response()->json([
            'success' => true,
            'estado'  => $request->estado,
            'mensaje' => "Todos los privilegios de {$request->caso_uso} {$accion}.",
            'ids'     => $permisos->pluck('id')->toArray(),
        ]);
    }

    public function togglePaquete(Request $request)
    {
        $request->validate([
            'id_rol'  => ['required', 'exists:rol,id'],
            'paquete' => ['required', 'string'],
            'estado'  => ['required', 'in:Activo,Inactivo'],
        ]);

        if ((int) $request->id_rol === 1) {
            return response()->json([
                'success' => false,
                'mensaje' => 'El rol base del sistema no puede modificarse.',
            ]);
        }

        $rol      = Rol::findOrFail($request->id_rol);
        $permisos = Permiso::where('paquete', $request->paquete)->get();

        foreach ($permisos as $permiso) {
            DB::table('rol_permiso')->updateOrInsert(
                ['id_rol' => $rol->id, 'id_permiso' => $permiso->id],
                ['estado' => $request->estado, 'fecha_registro' => now()->toDateString()]
            );
        }

        $accion = $request->estado === 'Activo' ? 'activaron' : 'desactivaron';
        Bitacora::registrar(
            'Cambio masivo de Privilegios',
            "Se {$accion} todos los privilegios del paquete para rol «{$rol->nombre}»"
        );

        return response()->json([
            'success' => true,
            'estado'  => $request->estado,
            'mensaje' => "Todos los privilegios del paquete {$accion}.",
            'ids'     => $permisos->pluck('id')->toArray(),
        ]);
    }
}