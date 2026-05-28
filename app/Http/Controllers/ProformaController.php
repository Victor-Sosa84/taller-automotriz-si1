<?php
// app/Http/Controllers/ProformaController.php
namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Diagnostico;
use App\Models\ManoObra;
use App\Models\OrdenTrabajo;
use App\Models\Proforma;
use App\Models\ProformaRepuesto;
use App\Models\ProformaServicio;
use App\Models\Repuesto;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ProformaController extends Controller
{
    // CU-06: formulario de nueva proforma a partir de un diagnóstico
    public function create(Request $request)
    {
        $diagnosticoId = $request->query('diagnostico_id');
        $diagnostico   = Diagnostico::with('auto')->findOrFail($diagnosticoId);
        $repuestos     = Repuesto::orderBy('nombre')->get();
        $servicios     = ManoObra::orderBy('descripcion')->get();
        return view('proforma.create', compact('diagnostico', 'repuestos', 'servicios'));
    }

    // CU-06: guardar proforma con repuestos y servicios
    public function store(Request $request)
    {
        $request->validate([
            'diagnostico_id'          => ['required', 'integer', 'exists:diagnostico,id'],
            'ci_cliente'              => ['required', 'string', 'exists:persona,ci'],
            'plazo'                   => ['nullable', 'date'],
            'repuestos'               => ['nullable', 'array'],
            'repuestos.*.id_repuesto' => ['required', 'integer', 'exists:repuesto,id'],
            'repuestos.*.cantidad'    => ['required', 'integer', 'min:1'],
            'repuestos.*.precio'      => ['required', 'numeric', 'min:0'],
            'repuestos.*.descuento'   => ['nullable', 'numeric', 'min:0', 'max:100'],
            'servicios'               => ['nullable', 'array'],
            'servicios.*.id_servicio' => ['required', 'integer', 'exists:mano_obra,id'],
            'servicios.*.cantidad'    => ['required', 'integer', 'min:1'],
            'servicios.*.costo'       => ['required', 'numeric', 'min:0'],
        ]);

        $proforma = Proforma::create([
            'ci_cliente'     => $request->ci_cliente,
            'id_diagnostico' => $request->diagnostico_id,
            'fecha'          => now(),
            'total_aprox'    => 0,
            'estado'         => 'Borrador',
            'plazo'          => $request->plazo,
        ]);

        foreach ($request->input('repuestos', []) as $r) {
            ProformaRepuesto::create([
                'nro_proforma'   => $proforma->nro,
                'id_repuesto'    => $r['id_repuesto'],
                'cantidad'       => $r['cantidad'],
                'precio_unitario'=> $r['precio'],
                'descuento'      => $r['descuento'] ?? 0,
            ]);
        }

        foreach ($request->input('servicios', []) as $s) {
            ProformaServicio::create([
                'nro_proforma' => $proforma->nro,
                'id_mano_obra' => $s['id_servicio'],
                'cantidad'     => $s['cantidad'],
                'costo'        => $s['costo'],
                'estado'       => 'Pendiente',
            ]);
        }

        $proforma->load('repuestos', 'servicios');
        $proforma->update(['total_aprox' => $proforma->calcularTotal()]);

        Bitacora::registrar('Elaborar Proforma', "Proforma #{$proforma->nro} - Diagnóstico #{$request->diagnostico_id}");

        return redirect()->route('proforma.show', $proforma->nro)
                    ->with('success', "Proforma #{$proforma->nro} creada correctamente.");
    }

    // Ver proforma con detalle
    public function show(Proforma $proforma)
    {
        $proforma->load('repuestos.repuesto', 'servicios.manoObra', 'diagnostico.auto', 'cliente');
        return view('proforma.show', compact('proforma'));
    }

    // CU-06: editar proforma (solo en estado Borrador)
    public function edit(Proforma $proforma)
    {
        if ($proforma->estado !== 'Borrador') {
            return back()->with('error', 'Solo se puede editar una proforma en estado Borrador.');
        }
        $proforma->load('repuestos.repuesto', 'servicios.manoObra');
        $repuestos = Repuesto::orderBy('nombre')->get();
        $servicios = ManoObra::orderBy('descripcion')->get();

        $repuestosExistentes = $proforma->repuestos->map(fn($r) => [
            'id_repuesto' => $r->id_repuesto,
            'cantidad'    => $r->cantidad,
            'precio'      => $r->precio_unitario,
            'descuento'   => $r->descuento,
        ]);

        $serviciosExistentes = $proforma->servicios->map(fn($s) => [
            'id_servicio' => $s->id_mano_obra,
            'cantidad'    => $s->cantidad,
            'costo'       => $s->costo,
        ]);

        return view('proforma.edit', compact(
            'proforma', 'repuestos', 'servicios',
            'repuestosExistentes', 'serviciosExistentes'
        ));
    }

    // CU-06: actualizar proforma
    public function update(Request $request, Proforma $proforma)
    {
        if ($proforma->estado !== 'Borrador') {
            return back()->with('error', 'Solo se puede editar una proforma en estado Borrador.');
        }

        $request->validate([
            'plazo'                   => ['nullable', 'date'],
            'repuestos'               => ['nullable', 'array'],
            'repuestos.*.id_repuesto' => ['required', 'integer', 'exists:repuesto,id'],
            'repuestos.*.cantidad'    => ['required', 'integer', 'min:1'],
            'repuestos.*.precio'      => ['required', 'numeric', 'min:0'],
            'repuestos.*.descuento'   => ['nullable', 'numeric', 'min:0', 'max:100'],
            'servicios'               => ['nullable', 'array'],
            'servicios.*.id_servicio' => ['required', 'integer', 'exists:mano_obra,id'],
            'servicios.*.cantidad'    => ['required', 'integer', 'min:1'],
            'servicios.*.costo'       => ['required', 'numeric', 'min:0'],
        ]);

        $proforma->repuestos()->delete();
        $proforma->servicios()->delete();

        foreach ($request->input('repuestos', []) as $r) {
            ProformaRepuesto::create([
                'nro_proforma'    => $proforma->nro,
                'id_repuesto'     => $r['id_repuesto'],
                'cantidad'        => $r['cantidad'],
                'precio_unitario' => $r['precio'],
                'descuento'       => $r['descuento'] ?? 0,
            ]);
        }

        foreach ($request->input('servicios', []) as $s) {
            ProformaServicio::create([
                'nro_proforma' => $proforma->nro,
                'id_mano_obra' => $s['id_servicio'],
                'cantidad'     => $s['cantidad'],
                'costo'        => $s['costo'],
                'estado'       => 'Pendiente',
            ]);
        }

        $proforma->load('repuestos', 'servicios');
        $proforma->update([
            'plazo'       => $request->plazo,
            'total_aprox' => $proforma->calcularTotal(),
        ]);

        Bitacora::registrar('Editar Proforma', "Proforma #{$proforma->nro} actualizada.");

        return redirect()->route('proforma.show', $proforma->nro)
                        ->with('success', 'Proforma actualizada correctamente.');
    }

    // CU-06: eliminar proforma
    public function destroy(Proforma $proforma)
    {
        if (!in_array($proforma->estado, ['Borrador', 'Observada'])) {
            return back()->with('error', 'No se puede eliminar una proforma en estado ' . $proforma->estado . '.');
        }
        $nro           = $proforma->nro;
        $diagnosticoId = $proforma->id_diagnostico;
        $proforma->delete();

        Bitacora::registrar('Eliminar Proforma', "Proforma #{$nro} eliminada.");

        return redirect()->route('diagnostico.show', $diagnosticoId)
                ->with('success', "Proforma #{$nro} eliminada correctamente.");
    }

    // CU-07: emitir cotización
    public function emitir(Proforma $proforma)
    {
        if ($proforma->estado !== 'Borrador') {
            return back()->with('error', 'Solo se puede emitir una proforma en estado Borrador.');
        }
        $proforma->update(['estado' => 'Emitida']);
        Bitacora::registrar('Emitir Cotización', "Proforma #{$proforma->nro} emitida.");
        return redirect()->route('proforma.show', $proforma->nro)
                        ->with('success', 'Cotización emitida correctamente.');
    }

    // CU-08: actualizar estado
    public function actualizarEstado(Request $request, Proforma $proforma)
    {
        $request->validate([
            'estado' => ['required', 'in:Borrador,Emitida,Aprobada,Observada,Anulada'],
        ]);

        $proforma->update(['estado' => $request->estado]);

        if ($request->estado === 'Aprobada') {
            $orden = OrdenTrabajo::where('nro_proforma', $proforma->nro)->first();
            if ($orden) {
                $orden->update(['estado' => 'Aprobada']);
            }
        }

        Bitacora::registrar('Gestionar Estado Proforma', "Proforma #{$proforma->nro} → {$request->estado}");

        return redirect()->route('proforma.show', $proforma->nro)
                        ->with('success', "Estado actualizado a {$request->estado}.");
    }

    public function index(Request $request)
    {
        $query = Proforma::with('diagnostico.auto')->latest('fecha');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('desde')) {
            $query->whereDate('fecha', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('fecha', '<=', $request->hasta);
        }
        if ($request->filled('placa')) {
            $query->whereHas('diagnostico.auto', function ($q) use ($request) {
                $q->where('placa', 'like', '%' . $request->placa . '%');
            });
        }

        $proformas = $query->paginate(15)->withQueryString();
        return view('proforma.index', compact('proformas'));
    }

    public function pdf(Proforma $proforma)
    {
        $proforma->load('repuestos.repuesto', 'servicios.manoObra', 'diagnostico.auto', 'cliente');
        $pdf = Pdf::loadView('proforma.pdf', compact('proforma'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download("proforma-{$proforma->nro}.pdf");
    }
}