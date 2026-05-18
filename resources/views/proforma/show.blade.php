@extends('layouts.app')
@section('title', 'Proforma #' . $proforma->nro)
@section('content')
<div style="max-width:900px;">

    {{-- Header --}}
    <div style="margin-bottom:1.5rem; display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
        <div>
            <a href="{{ route('diagnostico.show', $proforma->id_diagnostico) }}" 
                style="font-size:.8rem; color:var(--muted); text-decoration:none;">← Volver a diagnóstico</a>
            <div style="display:flex; align-items:center; gap:.75rem; margin-top:.5rem; flex-wrap:wrap;">
                <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; margin:0;">
                    Proforma #{{ $proforma->nro }}
                </h2>
                @php
                    $colores = [
                        'Borrador'  => 'background:rgba(107,117,145,.2); color:var(--muted);',
                        'Emitida'   => 'background:rgba(52,152,219,.15); color:#5dade2;',
                        'Aprobada'  => 'background:rgba(46,204,113,.15); color:var(--success);',
                        'Observada' => 'background:rgba(231,76,60,.15); color:var(--danger);',
                        'Anulada' => 'background:rgba(231,76,60,.1); color:var(--danger);',
                    ];
                    $estilo = $colores[$proforma->estado] ?? '';
                @endphp
                <span style="font-size:.75rem; font-weight:700; padding:.25rem .75rem; border-radius:999px; text-transform:uppercase; letter-spacing:.05em; {{ $estilo }}">
                    {{ $proforma->estado }}
                </span>
            </div>
            <p style="color:var(--muted); font-size:.9rem; margin-top:.4rem;">
                {{ $proforma->fecha?->format('d/m/Y H:i') }} —
                Diagnóstico #{{ $proforma->id_diagnostico }} —
                Vehículo {{ $proforma->diagnostico->auto->placa ?? '—' }}
            </p>
        </div>
        <div style="display:flex; gap:.5rem; flex-wrap:wrap; align-items:flex-start;">
            @if($proforma->estado === 'Borrador')
                <a href="{{ route('proforma.edit', $proforma->nro) }}" class="btn btn-ghost btn-sm">✏ Editar</a>
                <form method="POST" action="{{ route('proforma.emitir', $proforma->nro) }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm">Emitir Cotización</button>
                </form>
            @endif
            @php
                $transiciones = [
                    'Emitida'   => ['Aprobada', 'Observada', 'Anulada'],
                    'Aprobada'  => ['Anulada'],
                    'Observada' => ['Emitida', 'Anulada'],
                ];
                $opciones = $transiciones[$proforma->estado] ?? [];
            @endphp

            @if(count($opciones) > 0)
                <form method="POST" action="{{ route('proforma.estado', $proforma->nro) }}"
                    style="display:inline-flex; gap:.5rem;" id="form-estado">
                    @csrf
                    <select name="estado" style="background:var(--surface2); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); padding:.4rem .8rem; font-size:.85rem;">
                        @foreach($opciones as $opcion)
                            <option value="{{ $opcion }}">{{ $opcion }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-ghost btn-sm">Cambiar estado</button>
                </form>
            @endif
            @if(in_array($proforma->estado, ['Borrador', 'Observada']))
                <form method="POST" action="{{ route('proforma.destroy', $proforma->nro) }}" id="form-eliminar">
                    @csrf @method('DELETE')
                    <button type="button" class="btn btn-danger btn-sm" onclick="abrirModal()">Eliminar</button>
                </form>
            @endif
        </div>
    </div>

    {{-- Info general --}}
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header"><span class="card-title">Datos generales</span></div>
        <div class="card-body">
            <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1rem;">
                <div><strong>Cliente CI:</strong> {{ $proforma->ci_cliente }}</div>
                <div><strong>Plazo:</strong> {{ $proforma->plazo ? \Carbon\Carbon::parse($proforma->plazo)->format('d/m/Y') : '—' }}</div>
                <div><strong>Total:</strong> <span style="color:var(--accent); font-weight:700;">Bs {{ number_format($proforma->total_aprox, 2) }}</span></div>
            </div>
        </div>
    </div>

    {{-- Repuestos --}}
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header"><span class="card-title">Repuestos</span></div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Repuesto</th><th>Cantidad</th><th>Precio unit.</th><th>Descuento</th><th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proforma->repuestos as $r)
                        @php $sub = ($r->precio_unitario * $r->cantidad) * (1 - $r->descuento / 100); @endphp
                        <tr>
                            <td>{{ $r->repuesto->nombre ?? '—' }}</td>
                            <td>{{ $r->cantidad }}</td>
                            <td>Bs {{ number_format($r->precio_unitario, 2) }}</td>
                            <td>{{ $r->descuento }}%</td>
                            <td>Bs {{ number_format($sub, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="color:var(--muted); text-align:center;">Sin repuestos</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Servicios --}}
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header"><span class="card-title">Servicios / Mano de Obra</span></div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Servicio</th><th>Cantidad</th><th>Costo unit.</th><th>Subtotal</th></tr>
                </thead>
                <tbody>
                    @forelse($proforma->servicios as $s)
                        @php $sub = $s->costo * $s->cantidad; @endphp
                        <tr>
                            <td>{{ $s->manoObra->descripcion ?? '—' }}</td>
                            <td>{{ $s->cantidad }}</td>
                            <td>Bs {{ number_format($s->costo, 2) }}</td>
                            <td>Bs {{ number_format($sub, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="color:var(--muted); text-align:center;">Sin servicios</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Total --}}
    <div style="display:flex; justify-content:flex-end;">
        <div style="text-align:right; padding:1rem 1.5rem; background:var(--surface); border:1px solid var(--border); border-radius:var(--radius);">
            <div style="font-size:.8rem; color:var(--muted); text-transform:uppercase; letter-spacing:.06em; margin-bottom:.25rem;">Total aproximado</div>
            <div style="font-family:'Barlow Condensed',sans-serif; font-size:2.2rem; font-weight:800; color:var(--accent);">
                Bs {{ number_format($proforma->total_aprox, 2) }}
            </div>
        </div>
    </div>

</div>
{{-- Modal de confirmación --}}
<div id="modal-eliminar" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:500; align-items:center; justify-content:center;">
    <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:2rem; max-width:400px; width:90%; text-align:center;">
        <div style="font-size:1.5rem; margin-bottom:.75rem;">🗑</div>
        <h3 style="font-family:'Barlow Condensed',sans-serif; font-size:1.3rem; font-weight:800; margin-bottom:.5rem;">¿Eliminar proforma?</h3>
        <p style="color:var(--muted); font-size:.9rem; margin-bottom:1.5rem;">Esta acción no se puede deshacer.</p>
        <div style="display:flex; gap:.75rem; justify-content:center;">
            <button type="button" class="btn btn-ghost" onclick="cerrarModal()">Cancelar</button>
            <button type="button" class="btn btn-danger" onclick="document.getElementById('form-eliminar').submit()">Sí, eliminar</button>
        </div>
    </div>
</div>
<div id="modal-cancelar" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:500; align-items:center; justify-content:center;">
    <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:2rem; max-width:400px; width:90%; text-align:center;">
        <div style="font-size:1.5rem; margin-bottom:.75rem;">⚠️</div>
        <h3 style="font-family:'Barlow Condensed',sans-serif; font-size:1.3rem; font-weight:800; margin-bottom:.5rem;">¿Anular proforma?</h3>
        <p style="color:var(--muted); font-size:.9rem; margin-bottom:1.5rem;">Esta acción es permanente y no podrá revertirse.</p>
        <div style="display:flex; gap:.75rem; justify-content:center;">
            <button type="button" class="btn btn-ghost" onclick="document.getElementById('modal-cancelar').style.display='none'">Volver</button>
            <button type="button" class="btn btn-danger" onclick="document.getElementById('form-estado').submit()">Sí, anular</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function abrirModal() {
    const m = document.getElementById('modal-eliminar');
    m.style.display = 'flex';
}
function cerrarModal() {
    document.getElementById('modal-eliminar').style.display = 'none';
}
document.getElementById('modal-eliminar').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});
document.getElementById('form-estado')?.addEventListener('submit', function(e) {
    const estado = this.querySelector('select[name="estado"]').value;
    if (estado === 'Anulada') {
        e.preventDefault();
        document.getElementById('modal-cancelar').style.display = 'flex';
    }
});
</script>
@endpush