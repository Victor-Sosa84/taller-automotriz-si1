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

    public function create(Request $request)
    {
        $redirect      = $request->query('redirect');
        $diagnosticoId = $request->query('diagnostico_id');
        return view('clientes.create', compact('redirect', 'diagnosticoId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ci'        => ['required', 'string', 'max:8'],
            'nombre'    => ['required', 'string', 'max:100'],
            'telefono'  => ['nullable', 'string', 'digits:7'],
            'direccion' => ['nullable', 'string', 'max:255'],
        ]);

        $persona = Persona::where('ci', $request->ci)->first();

        if ($persona) {
            // CASO 1: Ya es cliente. Bloqueamos para que no crea que registró a alguien nuevo.
            if ($persona->es_cliente) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "La persona con CI {$request->ci} ya está registrada como cliente.");
            }

            // CASO 2: Existe pero solo como personal. Lo "ascendemos" a cliente y actualizamos sus datos.
            $persona->update([
                'nombre'      => $request->nombre,
                'telefono'    => $request->telefono,
                'direccion'   => $request->direccion,
                'es_cliente'  => true,
            ]);
            
            $mensaje = "La persona ya existía como personal y ahora también es cliente.";
        } else {
            // CASO 3: No existe. Registro limpio.
            Persona::create([
                'ci'          => $request->ci,
                'nombre'      => $request->nombre,
                'telefono'    => $request->telefono,
                'direccion'   => $request->direccion,
                'es_cliente'  => true,
                'es_personal' => false,
            ]);
            $mensaje = "Cliente «{$request->nombre}» registrado correctamente.";
        }

        Bitacora::registrar('Registro de Cliente', "CI: {$request->ci} — {$request->nombre}");

        if ($request->input('redirect') === 'proforma' && $request->input('diagnostico_id')) {
            return redirect()->route('proforma.create', [
                'diagnostico_id' => $request->input('diagnostico_id'),
                'ci_cliente'     => $request->ci,
            ])->with('success', $mensaje);
        }

        return redirect()->route('clientes.index')->with('success', $mensaje);
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
            'telefono'  => ['nullable', 'string', 'digits:7'],
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
