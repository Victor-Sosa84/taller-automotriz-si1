@extends('layouts.app')
@section('title', 'Asignaciones — OT #' . $orden->nro)

@section('content')
<div style="max-width:900px;">
    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('orden_trabajo.show', $orden->nro) }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">← Volver a la orden</a>
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; margin-top:.5rem;">
            Responsables de tareas — Orden de Trabajo #{{ $orden->nro }}
        </h2>
        <p style="color:var(--muted); font-size:.95rem; margin-top:.25rem;">
            {{ $orden->auto->placa ?? '—' }} &mdash; {{ $orden->proforma->cliente->nombre ?? 'Sin cliente' }}
        </p>
    </div>

    {{-- Asignaciones actuales --}}
    <div class="table-wrap" style="margin-bottom:1.5rem;">
        <div style="padding:.75rem 1rem; background:var(--surface2); border-bottom:1px solid var(--border);">
            <span style="font-family:'Barlow Condensed',sans-serif; font-size:.85rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--muted);">Responsables asignados</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Personal</th>
                    <th>Mano de Obra</th>
                    <th>Tipo de Participación</th>
                    @if($orden->puede_editarse)
                    <th style="text-align:center;">Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($orden->realiza as $asignacion)
                <tr style="vertical-align:middle;">
                    <td>{{ $asignacion->persona->nombre ?? '—' }}</td>
                    <td>{{ $asignacion->manoObra->descripcion ?? '—' }}</td>
                    <td>{{ $asignacion->tipo_participacion ?? '—' }}</td>
                    @if($orden->puede_editarse)
                    <td style="text-align:center;">
                        @if(auth()->user()->puede('CU15_MOD') && $orden->puede_editarse)
                        <button onclick="abrirEditar('{{ $asignacion->ci_personal }}', {{ $asignacion->id_mano_obra }}, '{{ $asignacion->tipo_participacion }}')"
                            class="btn btn-sm btn-ghost">Editar</button>
                        @endif
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $orden->puede_editarse ? 4 : 3 }}" style="text-align:center; color:var(--muted); padding:2rem;">No hay responsables asignados aún.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Formulario nueva asignación --}}
    @if(auth()->user()->puede('CU15_ADD') && $orden->puede_editarse)
    <div class="form-card" style="margin-bottom:1.5rem;">
        <div style="margin-bottom:1.25rem;">
            <span style="font-family:'Barlow Condensed',sans-serif; font-size:1rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em;">Nueva Asignación</span>
        </div>
        <form action="{{ route('asignacion.store', $orden->nro) }}" method="POST">
            @csrf
            <div class="form-grid">
                <div class="field-group">
                    <label for="ci_personal">Personal <span class="req">*</span></label>
                    <select id="ci_personal" name="ci_personal">
                        <option value="">Seleccionar...</option>
                        @foreach($personal as $p)
                        <option value="{{ $p->ci }}" {{ old('ci_personal') == $p->ci ? 'selected' : '' }}>
                            {{ $p->nombre }} ({{ $p->ci }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="field-group">
                    <label for="id_mano_obra">Mano de Obra <span class="req">*</span></label>
                    <select id="id_mano_obra" name="id_mano_obra">
                        <option value="">Seleccionar...</option>
                        @foreach($servicios as $s)
                        <option value="{{ $s->id }}" {{ old('id_mano_obra') == $s->id ? 'selected' : '' }}>
                            {{ $s->descripcion }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="field-group" style="grid-column:1 / -1;">
                    <label for="tipo_participacion">Tipo de Participación</label>
                    <input id="tipo_participacion" name="tipo_participacion" type="text"
                        value="{{ old('tipo_participacion') }}"
                        placeholder="Ej. Mecánico principal, Asistente..." />
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Registrar Asignación</button>
            </div>
        </form>
    </div>
    @endif
</div>

{{-- Modal editar tipo de participación --}}
<div id="modal-editar" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:500; align-items:center; justify-content:center;">
    <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:2rem; width:100%; max-width:420px; margin:1rem;">
        <h3 style="font-family:'Barlow Condensed',sans-serif; font-size:1.2rem; font-weight:700; margin-bottom:1.25rem;">Editar Tipo de Participación</h3>
        <form id="form-editar" method="POST">
            @csrf
            @method('PUT')
            <div class="field-group" style="margin-bottom:1.25rem;">
                <label for="edit_tipo">Tipo de Participación</label>
                <input id="edit_tipo" name="tipo_participacion" type="text" placeholder="Ej. Mecánico principal..." />
            </div>
            <div style="display:flex; gap:.75rem; justify-content:flex-end;">
                <button type="button" onclick="cerrarEditar()" class="btn btn-ghost" style="color:var(--muted);">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function abrirEditar(ci, idManoObra, tipoActual) {
    const modal = document.getElementById('modal-editar');
    const form = document.getElementById('form-editar');
    const input = document.getElementById('edit_tipo');

    form.action = `/ordenes/{{ $orden->nro }}/asignaciones/${ci}/${idManoObra}`;
    input.value = tipoActual !== 'null' ? tipoActual : '';
    modal.style.display = 'flex';
}
function cerrarEditar() {
    document.getElementById('modal-editar').style.display = 'none';
}
</script>
@endpush