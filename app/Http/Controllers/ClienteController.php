<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Persona;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Persona::where('es_cliente', true);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre',    'like', "%{$search}%")
                  ->orWhere('ci',       'like', "%{$search}%")
                  ->orWhere('telefono', 'like', "%{$search}%");
            });
        }

        $clientes = $query->orderBy('nombre')->paginate(12)->withQueryString();

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ci'        => ['required', 'string', 'max:20', 'unique:persona,ci'],
            'nombre'    => ['required', 'string', 'max:100'],
            'telefono'  => ['nullable', 'string', 'max:20'],
            'direccion' => ['nullable', 'string', 'max:255'],
        ]);

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

        Bitacora::registrar('Registro de Cliente', "CI: {$request->ci} — {$request->nombre}");

        return redirect()->route('clientes.index')
                         ->with('success', "Cliente «{$request->nombre}» registrado correctamente.");
    }

    public function show(string $ci)
    {
        $cliente = Persona::where('es_cliente', true)->findOrFail($ci);

        // Vehículos del cliente vía diagnósticos
        $vehiculos = \App\Models\Diagnostico::where('ci_personal', $ci)
            ->with('auto')
            ->get()
            ->pluck('auto')
            ->filter()
            ->unique('placa')
            ->values();

        // Historial de diagnósticos del cliente
        $historial = \App\Models\Diagnostico::where('ci_personal', $ci)
            ->with(['auto', 'proforma.ordenTrabajo'])
            ->orderByDesc('fecha')
            ->get();

        return view('clientes.show', compact('cliente', 'vehiculos', 'historial'));
    }

    public function edit(string $ci)
    {
        $cliente = Persona::where('es_cliente', true)->findOrFail($ci);
        return view('clientes.edit', compact('cliente'));
    }

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

        Bitacora::registrar('Edición de Cliente', "CI: {$ci} — {$request->nombre}");

        return redirect()->route('clientes.index')
                         ->with('success', "Cliente «{$request->nombre}» actualizado correctamente.");
    }
}
