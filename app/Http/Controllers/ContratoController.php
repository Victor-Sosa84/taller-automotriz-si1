<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\Persona;
use App\Models\TipoRemuneracion;
use Illuminate\Http\Request;

class ContratoController extends Controller
{
    // +listarContratos()
    public function listarContratos()
    {
        // Se obtienen todos los contratos con sus relaciones para la vista
        $contratos = Contrato::with(['personal', 'modalidadRemuneracion'])->get();
        $personalDisponible = Persona::where('es_personal', true)->get();
        $tiposRemuneracion = TipoRemuneracion::all();

        return view('contratos.index', compact('contratos', 'personalDisponible', 'tiposRemuneracion'));
    }

    // +crearContrato()
    public function crearContrato(Request $request)
    {
        $request->validate([
            'ci_personal' => 'required|string|exists:persona,ci',
            'tipo_remuneracion' => 'required|integer|exists:tipo_remuneracion,nro',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'periodo_pago' => 'required|string|max:50',
            'valor' => 'required|numeric|min:0',
        ]);

        // Validación de negocio: Evitar contratos vigentes duplicados para la misma persona
        $contratoActivo = Contrato::where('ci_personal', $request->ci_personal)
            ->where('estado', 'Vigente')
            ->exists();

        if ($contratoActivo) {
            return redirect()->back()->with('error', 'El empleado ya cuenta con un contrato activo vigente.');
        }

        Contrato::create([
            'ci_personal' => $request->ci_personal,
            'tipo_remuneracion' => $request->tipo_remuneracion,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'estado' => 'Vigente',
            'periodo_pago' => $request->periodo_pago,
            'valor' => $request->valor,
        ]);

        return redirect()->route('contratos.index')->with('success', 'Contrato laboral creado exitosamente.');
    }

    // +verContrato()
    public function verContrato($id)
    {
        $contrato = Contrato::with(['personal', 'modalidadRemuneracion', 'pagos'])->findOrFail($id);
        return response()->json($contrato);
    }

    // +actualizarContrato()
    public function actualizarContrato(Request $request, $id)
    {
        $contrato = Contrato::findOrFail($id);

        $request->validate([
            'tipo_remuneracion' => 'required|integer|exists:tipo_remuneracion,nro',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'periodo_pago' => 'required|string|max:50',
            'valor' => 'required|numeric|min:0',
            'estado' => 'required|string|max:20'
        ]);

        $contrato->update($request->all());

        return redirect()->route('contratos.index')->with('success', 'Contrato actualizado correctamente.');
    }

    // +darBajaContrato()
    public function darBajaContrato($id)
    {
        $contrato = Contrato::findOrFail($id);
        
        // Cambio lógico de estado como se acostumbra en tu sistema
        $contrato->update([
            'estado' => 'Finalizado',
            'fecha_fin' => now()->toDateString()
        ]);

        return redirect()->route('contratos.index')->with('success', 'El contrato ha sido finalizado correctamente.');
    }
}