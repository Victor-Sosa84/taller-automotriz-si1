<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    // ── INDEX ────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Persona::where('es_cliente', true);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre',   'like', "%{$search}%")
                  ->orWhere('ci',      'like', "%{$search}%")
                  ->orWhere('telefono','like', "%{$search}%");
            });
        }

        $clientes = $query->orderBy('nombre')->paginate(12)->withQueryString();

        return view('clientes.index', compact('clientes'));
    }

    // ── CREATE ───────────────────────────────────────────────────
    public function create()
    {
        return view('clientes.create');
    }

    // ── STORE ────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'ci'        => ['required', 'string', 'max:20', 'unique:persona,ci'],
            'nombre'    => ['required', 'string', 'max:100'],
            'telefono'  => ['nullable', 'string', 'max:20'],
            'direccion' => ['nullable', 'string', 'max:255'],
        ]);

        // Si la persona ya existe como personal, la marcamos también como cliente
        $persona = Persona::find($request->ci);

        if ($persona) {
            $persona->update(['es_cliente' => true]);
        } else {
            Persona::create([
                'ci'          => $request->ci,
                'nombre'      => $request->nombre,
                'telefono'    => $request->telefono,
                'direccion'   => $request->direccion,
                'es_cliente'  => true,
                'es_personal' => false,
            ]);
        }

        return redirect()->route('clientes.index')
                         ->with('success', "Cliente «{$request->nombre}» registrado correctamente.");
    }

    // ── EDIT ─────────────────────────────────────────────────────
    public function edit(string $ci)
    {
        $cliente = Persona::where('es_cliente', true)->findOrFail($ci);
        return view('clientes.edit', compact('cliente'));
    }

    // ── UPDATE ───────────────────────────────────────────────────
    public function update(Request $request, string $ci)
    {
        $cliente = Persona::where('es_cliente', true)->findOrFail($ci);

        $request->validate([
            'nombre'    => ['required', 'string', 'max:100'],
            'telefono'  => ['nullable', 'string', 'max:20'],
            'direccion' => ['nullable', 'string', 'max:255'],
        ]);

        $cliente->update([
            'nombre'    => $request->nombre,
            'telefono'  => $request->telefono,
            'direccion' => $request->direccion,
        ]);

        return redirect()->route('clientes.index')
                         ->with('success', "Cliente «{$request->nombre}» actualizado correctamente.");
    }

    // ── SHOW ─────────────────────────────────────────────────────
    // Vista de perfil del cliente — desde aquí se accederá a sus vehículos
    public function show(string $ci)
    {
        $cliente = Persona::where('es_cliente', true)
                          ->with([
                              // En el futuro: autos via diagnostico
                          ])
                          ->findOrFail($ci);

        return view('clientes.show', compact('cliente'));
    }
}
