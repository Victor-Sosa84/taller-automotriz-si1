@extends('layouts.app')
@section('title', 'Préstamos de Herramientas')

@section('content')
<div style="max-width:960px;">
    <div style="margin-bottom:1.5rem;">
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; margin-top:.5rem;">Préstamos de Herramientas</h2>
        <p style="color:var(--muted); font-size:.95rem; margin-top:.25rem;">Registro y seguimiento de préstamos de herramientas del taller.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    {{-- Formulario nuevo préstamo --}}
    @if(auth()->user()->puede('CU09_ADD'))
    <div class="form-card" style="margin-bottom:1.5rem;">
        <div style="margin-bottom:1.25rem;">
            <span style="font-family:'Barlow Condensed',sans-serif; font-size:1rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em;">Registrar Préstamo</span>
        </div>
        <form action="{{ route('prestamo.store') }}" method="POST">
            @csrf
            <div class="form-grid">
                <div class="field-group">
                    <label for="nro_herramienta">Herramienta <span class="req">*</span></label>
                    <select id="nro_herramienta" name="nro_herramienta" required>
                        <option value="">Seleccionar...</option>
                        @foreach($herramientas as $h)
                        <option value="{{ $h->nro }}">{{ $h->descripcion }} — {{ $h->tipo->descripcion ?? '' }} / {{ $h->marca->nombre ?? '' }}</option>
                        @endforeach
                    </select>
                    @if($herramientas->isEmpty())
                        <span style="font-size:.75rem; color:var(--danger);">No hay herramientas disponibles.</span>
                    @endif
                </div>
                <div class="field-group">
                    <label for="fecha_salida">Fecha de Salida <span class="req">*</span></label>
                    <input id="fecha_salida" name="fecha_salida" type="datetime-local"
                        value="{{ now()->format('Y-m-d\TH:i') }}" required />
                </div>
                <div class="field-group" style="grid-column:1 / -1;">
                    <label>Estado actual de la herramienta</label>
                    <span id="estado-herramienta" style="font-size:.9rem; color:var(--muted);">
                        Seleccioná una herramienta para ver su estado.
                    </span>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Registrar Préstamo</button>
            </div>
        </form>
    </div>
    @endif

    {{-- Lista de préstamos --}}
    <div class="table-wrap">
        <div style="padding:.75rem 1rem; background:var(--surface2); border-bottom:1px solid var(--border);">
            <span style="font-family:'Barlow Condensed',sans-serif; font-size:.85rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--muted);">Préstamos registrados</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Herramienta</th>
                    <th>Estado Salida</th>
                    <th>Fecha Salida</th>
                    <th>Fecha Devolución</th>
                    <th>Estado Retorno</th>
                    <th style="text-align:center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($prestamos as $p)
                    @foreach($p->detalles as $d)
                    <tr style="vertical-align:middle;">
                        <td class="td-muted">{{ $p->id }}</td>
                        <td>{{ $d->herramienta->descripcion ?? '—' }}</td>
                        <td>{{ $d->estado_salida ?? '—' }}</td>
                        <td>{{ $p->fecha_salida->format('d/m/Y H:i') }}</td>
                        <td>{{ $p->fecha_devolucion ? $p->fecha_devolucion->format('d/m/Y H:i') : '—' }}</td>
                        <td>
                            @if($d->estado_retorno)
                                <span style="color:var(--success);">{{ $d->estado_retorno }}</span>
                            @else
                                <span style="color:var(--accent);">Pendiente</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            @if(!$p->fecha_devolucion && auth()->user()->puede('CU10_MOD'))
                            <button onclick="abrirDevolucion({{ $p->id }})" class="btn btn-sm btn-primary">Registrar Devolución</button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; color:var(--muted); padding:2rem;">No hay préstamos registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="modal-devolucion" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:500; align-items:center; justify-content:center;">
    <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:2rem; width:100%; max-width:460px; margin:1rem;">
        <h3 style="font-family:'Barlow Condensed',sans-serif; font-size:1.2rem; font-weight:700; margin-bottom:1.25rem;">Registrar Devolución</h3>
        <form id="form-devolucion" method="POST">
            @csrf @method('PUT')
            <div class="form-grid">
                <div class="field-group">
                    <label>Fecha de Devolución <span class="req">*</span></label>
                    <input id="dev-fecha" name="fecha_devolucion" type="datetime-local"
                        value="{{ now()->format('Y-m-d\TH:i') }}" required />
                </div>
                <div class="field-group">
                    <label>Estado de Retorno <span class="req">*</span></label>
                    <select id="dev-estado-retorno" name="estado_retorno">
                        <option value="Bueno">Bueno</option>
                        <option value="Regular">Regular</option>
                        <option value="Malo">Malo</option>
                    </select>
                </div>
                <div class="field-group" style="grid-column:1/-1;">
                    <label>Estado de la Herramienta <span class="req">*</span></label>
                    <select name="estado">
                        <option value="Bueno">Bueno</option>
                        <option value="Regular">Regular</option>
                        <option value="Malo">Malo</option>
                    </select>
                </div>
            </div>
            <div style="display:flex; gap:.75rem; justify-content:flex-end; margin-top:1.25rem;">
                <button type="button" onclick="cerrarDevolucion()" class="btn btn-ghost" style="color:var(--muted);">Cancelar</button>
                <button type="submit" class="btn btn-primary">Confirmar Devolución</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function abrirDevolucion(id) {
    document.getElementById('form-devolucion').action = `/prestamos/${id}/devolucion`;
    document.getElementById('modal-devolucion').style.display = 'flex';
}
function cerrarDevolucion() {
    document.getElementById('modal-devolucion').style.display = 'none';
}

document.getElementById('nro_herramienta').addEventListener('change', function() {
    const estados = {
        @foreach($herramientas as $h)
        {{ $h->nro }}: '{{ $h->estado }}',
        @endforeach
    };
    const estado = estados[this.value] || '—';
    const color = estado === 'Bueno' ? 'var(--success)' : estado === 'Regular' ? 'var(--accent)' : 'var(--danger)';
    document.getElementById('estado-herramienta').innerHTML = 
        `<span class="badge" style="background:rgba(0,0,0,.2);color:${color};border:1px solid ${color};">${estado}</span>`;
});
</script>
@endpush