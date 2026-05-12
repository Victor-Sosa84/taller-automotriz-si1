<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Persona;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UsuarioController extends Controller
{
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

    public function create()
    {
        $roles = Rol::all();
        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ci'         => ['required', 'string', 'max:8'], // Sin unique para permitir la búsqueda
            'nombre'     => ['required', 'string', 'max:100'],
            'telefono'   => ['nullable', 'string', 'digits:7'],
            'direccion'  => ['nullable', 'string', 'max:255'],
            'id_rol'     => ['required', 'exists:rol,id'],
            'correo'     => ['nullable', 'email', 'max:100', 'unique:usuario,correo'],
            'clave'      => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        return DB::transaction(function () use ($request) {
            // 1. Buscamos si la persona ya existe
            $persona = Persona::where('ci', $request->ci)->first();

            if ($persona) {
                // 2. VERIFICACIÓN CRÍTICA: ¿Ya tiene una cuenta de usuario?
                $usuarioExistente = Usuario::where('ci_personal', $persona->ci)->first();
                
                if ($usuarioExistente) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "La persona con CI {$request->ci} ya tiene una cuenta de usuario.");
                }

                // 3. Si existe pero no tiene usuario (era solo cliente), la actualizamos
                $persona->update([
                    'nombre'      => $request->nombre,
                    'telefono'    => $request->telefono ?? $persona->telefono,
                    'direccion'   => $request->direccion ?? $persona->direccion,
                    'es_personal' => true,
                ]);
            } else {
                // 4. Si no existe, creamos la persona desde cero
                $persona = Persona::create([
                    'ci'          => $request->ci,
                    'nombre'      => $request->nombre,
                    'telefono'    => $request->telefono,
                    'direccion'   => $request->direccion,
                    'es_cliente'  => false,
                    'es_personal' => true,
                ]);
            }

            // 5. Generar nombre de usuario único (tu lógica actual que está muy bien)
            $partes         = explode(' ', strtolower(trim($request->nombre)));
            $base           = Str::slug(substr($partes[0], 0, 1) . ($partes[1] ?? $partes[0]), '');
            $nombreUsuario  = $base;
            $i = 1;
            while (Usuario::where('nombre_usuario', $nombreUsuario)->exists()) {
                $nombreUsuario = $base . $i++;
            }

            // 6. Creamos el usuario vinculado a la persona
            Usuario::create([
                'id_rol'         => $request->id_rol,
                'ci_personal'    => $persona->ci,
                'nombre_usuario' => $nombreUsuario,
                'clave'          => Hash::make($request->clave),
                'correo'         => $request->correo,
            ]);

            Bitacora::registrar('Registro de Usuario', "usuario: {$nombreUsuario} — CI: {$persona->ci}");

            return redirect()->route('usuarios.index')
                            ->with('success', "Usuario «{$nombreUsuario}» creado exitosamente.");
        });
    }

    public function edit(int $id)
    {
        $usuario = Usuario::with(['persona', 'rol'])->findOrFail($id);
        $roles   = Rol::all();
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, int $id)
    {
        $usuario = Usuario::with('persona')->findOrFail($id);
 
        $esSiMismo   = (int)$id === (int)auth()->user()->id_usuario;
        $esAdminBase = (int)$usuario->id_usuario === 1;
        $rolProtegido = $esSiMismo || $esAdminBase;
 
        $request->validate([
            'nombre'    => ['required', 'string', 'max:100'],
            'telefono'  => ['nullable', 'string', 'digits:7'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'id_rol'    => ['required', 'exists:rol,id'],
            'correo'    => ['nullable', 'email', 'max:100',
                Rule::unique('usuario', 'correo')->ignore($id, 'id_usuario'),
            ],
            'clave' => ['nullable', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);
 
        // Bloquear cambio de rol si aplica
        if ($rolProtegido && (int)$request->id_rol !== (int)$usuario->id_rol) {
            return back()->withInput()
                         ->with('error', $esSiMismo
                             ? 'No puedes cambiar tu propio rol.'
                             : 'El rol del Admin Principal no puede modificarse.');
        }
 
        DB::transaction(function () use ($request, $usuario, $rolProtegido) {
            $usuario->persona->update([
                'nombre'    => $request->nombre,
                'telefono'  => $request->telefono,
                'direccion' => $request->direccion,
            ]);
 
            $datos = ['correo' => $request->correo];
            if (!$rolProtegido) {
                $datos['id_rol'] = $request->id_rol;
            }
            if ($request->filled('clave')) {
                $datos['clave'] = Hash::make($request->clave);
            }
            $usuario->update($datos);
 
            Bitacora::registrar('Edición de Usuario', "usuario: {$usuario->nombre_usuario}");
        });
 
        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(int $id)
    {
        $usuario = Usuario::with('persona')->findOrFail($id);

        if ($usuario->id_usuario === Auth::user()->id_usuario) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $nombre = $usuario->nombre_usuario;

        DB::transaction(function () use ($usuario) {
            Bitacora::registrar(
                'Eliminación de Usuario',
                "usuario: {$usuario->nombre_usuario}"
            );

            $usuario->delete();

            $persona = $usuario->persona;
            if ($persona && !$persona->es_cliente) {
                $persona->update(['es_personal' => false]);
            }
        });

        return redirect()->route('usuarios.index')
                        ->with('success', "Usuario «{$nombre}» eliminado.");
    }
}
