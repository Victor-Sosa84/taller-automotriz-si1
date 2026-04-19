<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Persona;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;

class UsuarioController extends Controller
{
    // ── INDEX ────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Usuario::with(['persona', 'rol']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre_usuario', 'like', "%{$search}%")
                  ->orWhere('correo', 'like', "%{$search}%")
                  ->orWhereHas('persona', fn($p) =>
                      $p->where('nombre', 'like', "%{$search}%")
                        ->orWhere('ci', 'like', "%{$search}%")
                  );
            });
        }

        if ($rol = $request->get('rol')) {
            $query->where('id_rol', $rol);
        }

        $usuarios = $query->orderByDesc('id_usuario')->paginate(10)->withQueryString();
        $roles    = Rol::all();

        return view('usuarios.index', compact('usuarios', 'roles'));
    }

    // ── CREATE ───────────────────────────────────────────────────
    public function create()
    {
        $roles = Rol::all();
        return view('usuarios.create', compact('roles'));
    }

    // ── STORE ────────────────────────────────────────────────────
    // Primero inserta en persona, luego en usuario (respeta FK)
    public function store(Request $request)
    {
        $request->validate([
            // Datos de persona
            'ci'          => ['required', 'string', 'max:20', 'unique:persona,ci'],
            'nombre'      => ['required', 'string', 'max:100'],
            'telefono'    => ['nullable', 'string', 'max:20'],
            'direccion'   => ['nullable', 'string', 'max:255'],
            // Datos de usuario
            'id_rol'          => ['required', 'exists:rol,id'],
            // 'nombre_usuario'  => ['required', 'string', 'max:50', 'unique:usuario,nombre_usuario'],
            'correo'          => ['nullable', 'email', 'max:100', 'unique:usuario,correo'],
            'clave'           => ['required', 'confirmed', Password::defaults()],
        ]);

        DB::transaction(function () use ($request) {
            // 1. Crear o actualizar persona (puede existir como cliente ya)
            $persona = Persona::firstOrCreate(
                ['ci' => $request->ci],
                [
                    'nombre'    => $request->nombre,
                    'telefono'  => $request->telefono,
                    'direccion' => $request->direccion,
                    'es_cliente'  => false,
                    'es_personal' => true,
                ]
            );

            // Si la persona ya existía (era cliente), la marcamos también como personal
            if (!$persona->wasRecentlyCreated) {
                $persona->update([
                    'es_personal' => true,
                    'nombre'      => $request->nombre,
                    'telefono'    => $request->telefono ?? $persona->telefono,
                    'direccion'   => $request->direccion ?? $persona->direccion,
                ]);
            }

            // 1. Limpiar el nombre completo y pasarlo a minúsculas
            $nombreCompleto = strtolower(trim($request->nombre));
            $partes = explode(' ', $nombreCompleto);

            // 2. Tomar la inicial del primer nombre y el primer apellido disponible
            $primerNombre = $partes[0];
            $primerApellido = (count($partes) > 1) ? $partes[1] : $partes[0];

            // 3. Unir y limpiar (quita tildes y caracteres raros)
            // Ejemplo: "Víctor Arispe" -> "varispe"
            $baseUsuario = Str::slug(substr($primerNombre, 0, 1) . $primerApellido, '');

            // 4. Verificar si ya existe para añadir un número si es necesario
            $nombreGenerado = $baseUsuario;
            $i = 1;
            while (Usuario::where('nombre_usuario', $nombreGenerado)->exists()) {
                $nombreGenerado = $baseUsuario . $i;
                $i++;
            }

            // 5. Crear el usuario con el nombre automático
            $usuario = Usuario::create([
                'id_rol'         => $request->id_rol,
                'ci_personal'    => $persona->ci,
                'nombre_usuario' => $nombreGenerado,
                'clave'          => Hash::make($request->clave),
                'correo'         => $request->correo,
            ]);

            // 3. Registrar en bitácora
            Bitacora::registrar(
                'Registro de Nuevo Usuario',
                auth()->user()->id_usuario
            );
        });

        return redirect()->route('usuarios.index')
                         ->with('success', "Usuario «{$request->nombre_usuario}» creado exitosamente.");
    }

    // ── EDIT ─────────────────────────────────────────────────────
    public function edit(int $id)
    {
        $usuario = Usuario::with(['persona', 'rol'])->findOrFail($id);
        $roles   = Rol::all();
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    // ── UPDATE ───────────────────────────────────────────────────
    public function update(Request $request, int $id)
    {
        $usuario = Usuario::with('persona')->findOrFail($id);

        $request->validate([
            // Datos de persona
            'nombre'    => ['required', 'string', 'max:100'],
            'telefono'  => ['nullable', 'string', 'max:20'],
            'direccion' => ['nullable', 'string', 'max:255'],
            // Datos de usuario
            'id_rol'         => ['required', 'exists:rol,id'],
            // 'nombre_usuario' => ['required', 'string', 'max:50',
            //     Rule::unique('usuario', 'nombre_usuario')->ignore($id, 'id_usuario'),
            // ],
            'correo' => ['nullable', 'email', 'max:100',
                Rule::unique('usuario', 'correo')->ignore($id, 'id_usuario'),
            ],
            'clave' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        DB::transaction(function () use ($request, $usuario) {
            // Actualizar persona
            $usuario->persona->update([
                'nombre'    => $request->nombre,
                'telefono'  => $request->telefono,
                'direccion' => $request->direccion,
            ]);

            // Actualizar usuario
            $datosUsuario = [
                'id_rol'         => $request->id_rol,
                // 'nombre_usuario' => $request->nombre_usuario,
                'correo'         => $request->correo,
            ];

            if ($request->filled('clave')) {
                $datosUsuario['clave'] = Hash::make($request->clave);
            }

            $usuario->update($datosUsuario);
        });

        return redirect()->route('usuarios.index')
                         ->with('success', "Usuario «{$request->nombre_usuario}» actualizado correctamente.");
    }

    // ── DESTROY ──────────────────────────────────────────────────
    public function destroy(int $id)
    {
        $usuario = Usuario::with('persona')->findOrFail($id);

        // No permitir que el admin se elimine a sí mismo
        if ($usuario->id_usuario === auth()->user()->id_usuario) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $nombre = $usuario->nombre_usuario;

        DB::transaction(function () use ($usuario) {
            // La bitácora tiene ON DELETE CASCADE, pero registramos antes de borrar
            Bitacora::registrar(
                'Eliminación de Usuario',
                auth()->user()->id_usuario
            );

            // Eliminar usuario (la persona se mantiene — puede ser cliente también)
            $usuario->delete();

            // Si la persona ya no es cliente, también la marcamos como no-personal
            $persona = $usuario->persona;
            if ($persona && !$persona->es_cliente) {
                $persona->update(['es_personal' => false]);
            }
        });

        return redirect()->route('usuarios.index')
                         ->with('success', "Usuario «{$nombre}» eliminado.");
    }
}
