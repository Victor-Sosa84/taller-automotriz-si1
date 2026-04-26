<?php

namespace App\Http\Controllers;

use App\Models\Auto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AutoController extends Controller
{
    // ── INDEX ────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Auto::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('placa',  'like', "%{$search}%")
                  ->orWhere('marca',  'like', "%{$search}%")
                  ->orWhere('modelo', 'like', "%{$search}%")
                  ->orWhere('color',  'like', "%{$search}%");
            });
        }

        $autos = $query->orderBy('placa')->paginate(12)->withQueryString();

        return view('autos.index', compact('autos'));
    }

    // ── CREATE ───────────────────────────────────────────────────
    public function create()
    {
        return view('autos.create');
    }

    // ── STORE ────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'placa'  => ['required', 'string', 'max:10', 'unique:auto,placa'],
            'marca'  => ['nullable', 'string', 'max:50'],
            'modelo' => ['nullable', 'string', 'max:50'],
            'anio'   => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'color'  => ['nullable', 'string', 'max:30'],
        ]);

        Auto::create($request->only(['placa', 'marca', 'modelo', 'anio', 'color']));

        return redirect()->route('autos.index')
                         ->with('success', "Vehículo con placa «{$request->placa}» registrado correctamente.");
    }

    // ── SHOW ─────────────────────────────────────────────────────
    public function show(string $placa)
    {
        $auto = Auto::with(['diagnosticos.persona'])->findOrFail($placa);
        return view('autos.show', compact('auto'));
    }

    // ── EDIT ─────────────────────────────────────────────────────
    public function edit(string $placa)
    {
        $auto = Auto::findOrFail($placa);
        return view('autos.edit', compact('auto'));
    }

    // ── UPDATE ───────────────────────────────────────────────────
    public function update(Request $request, string $placa)
    {
        $auto = Auto::findOrFail($placa);

        $request->validate([
            'marca'  => ['nullable', 'string', 'max:50'],
            'modelo' => ['nullable', 'string', 'max:50'],
            'anio'   => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'color'  => ['nullable', 'string', 'max:30'],
        ]);

        $auto->update($request->only(['marca', 'modelo', 'anio', 'color']));

        return redirect()->route('autos.index')
                         ->with('success', "Vehículo «{$placa}» actualizado correctamente.");
    }

    // ── DESTROY ──────────────────────────────────────────────────
    // Solo se permite eliminar si no tiene diagnósticos asociados
    public function destroy(string $placa)
    {
        $auto = Auto::findOrFail($placa);

        if ($auto->tieneDiagnosticos()) {
            return back()->with('error',
                "No se puede eliminar el vehículo «{$placa}» porque tiene diagnósticos u órdenes de trabajo registrados.");
        }

        $auto->delete();

        return redirect()->route('autos.index')
                         ->with('success', "Vehículo «{$placa}» eliminado.");
    }
}
