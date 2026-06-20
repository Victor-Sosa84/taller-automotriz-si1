@extends('layouts.app')
@section('title', 'Detalles — OT #' . $orden->nro)

@section('content')
<div style="max-width:960px;">
    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('orden_trabajo.show', $orden->nro) }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">← Volver a la orden</a>
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; margin-top:.5rem;">
            Detalles — Orden de Trabajo #{{ $orden->nro }}
        </h2>
        <p style="color:var(--muted); font-size:.95rem; margin-top:.25rem;">
            {{ $orden->auto->placa ?? '—' }} &mdash; {{ $orden->proforma->cliente->nombre ?? 'Sin cliente' }}
        </p>
    </div>

    {{-- Repuestos --}}
    <div class="table-wrap" style="margin-bottom:1.5rem;">
        <div style="padding:.75rem 1rem; background:var(--surface2); border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
            <span style="font-family:'Barlow Condensed',sans-serif; font-size:.85rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--muted);">Repuestos utilizados</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Repuesto</th>
                    <th>Cantidad</th>
                    <th>Precio Unit.</th>
                    <th>Descuento</th>
                    <th>Subtotal</th>
                    @if($orden->puede_editarse)
                    <th style="text-align:center;">Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($orden->detallesRepuesto as $dr)
                <tr style="vertical-align:middle;">
                    <td>{{ $dr->repuesto->nombre ?? '—' }}</td>
                    <td>{{ $dr->cantidad }}</td>
                    <td>Bs. {{ number_format($dr->precio_unitario, 2) }}</td>
                    <td>{{ $dr->descuento }}%</td>
                    <td>Bs. {{ number_format($dr->cantidad * $dr->precio_unitario * (1 - $dr->descuento / 100), 2) }}</td>
                    @if($orden->puede_editarse)
                    <td style="text-align:center; white-space:nowrap;">
                        @if(auth()->user()->puede('CU16_MOD') && $orden->puede_editarse)
                        <button onclick="abrirEditarRep({{ $dr->id_repuesto }}, {{ $dr->cantidad }}, {{ $dr->precio_unitario }}, {{ $dr->descuento }})"
                            class="btn btn-sm btn-ghost">Editar</button>
                        @endif
                        @if(auth()->user()->puede('CU16_DEL') && $orden->puede_editarse)
                        <form method="POST" action="{{ route('detalle_ot.destroy', [$orden->nro, 'repuesto', $dr->id_repuesto]) }}" style="display:inline;"
                            onsubmit="return confirm('¿Eliminar este repuesto?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                        @endif
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $orden->puede_editarse ? 6 : 5 }}" style="text-align:center; color:var(--muted); padding:1.5rem;">Sin repuestos registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mano de Obra --}}
    <div class="table-wrap" style="margin-bottom:1.5rem;">
        <div style="padding:.75rem 1rem; background:var(--surface2); border-bottom:1px solid var(--border);">
            <span style="font-family:'Barlow Condensed',sans-serif; font-size:.85rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--muted);">Mano de obra ejecutada</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Servicio</th>
                    <th>Cantidad</th>
                    <th>Costo</th>
                    <th>Estado</th>
                    @if($orden->puede_editarse)
                    <th style="text-align:center;">Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($orden->detallesTrabajo as $dt)
                <tr style="vertical-align:middle;">
                    <td>{{ $dt->manoObra->descripcion ?? '—' }}</td>
                    <td>{{ $dt->cantidad }}</td>
                    <td>Bs. {{ number_format($dt->costo, 2) }}</td>
                    <td>{{ $dt->estado ?? '—' }}</td>
                    @if($orden->puede_editarse)
                    <td style="text-align:center; white-space:nowrap;">
                        @if(auth()->user()->puede('CU16_MOD') && $orden->puede_editarse)
                        <button onclick="abrirEditarMO({{ $dt->id_mano_obra }}, {{ $dt->cantidad }}, {{ $dt->costo }}, '{{ $dt->estado }}')"
                            class="btn btn-sm btn-ghost">Editar</button>
                        @endif
                        @if(auth()->user()->puede('CU16_DEL') && $orden->puede_editarse)
                        <form method="POST" action="{{ route('detalle_ot.destroy', [$orden->nro, 'mano_obra', $dt->id_mano_obra]) }}" style="display:inline;"
                            onsubmit="return confirm('¿Eliminar este servicio?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                        @endif
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $orden->puede_editarse ? 5 : 4 }}" style="text-align:center; color:var(--muted); padding:1.5rem;">Sin mano de obra registrada.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Formulario nuevo detalle --}}
    @if(auth()->user()->puede('CU16_ADD') && $orden->puede_editarse)
    <div class="form-card">
        <div style="margin-bottom:1.25rem;">
            <span style="font-family:'Barlow Condensed',sans-serif; font-size:1rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em;">Registrar Detalle</span>
        </div>
        <form action="{{ route('detalle_ot.store', $orden->nro) }}" method="POST" id="form-detalle">
            @csrf
            <div class="form-grid">
                <div class="field-group" style="grid-column:1 / -1;">
                    <label>Tipo <span class="req">*</span></label>
                    <div style="display:flex; gap:1rem;">
                        <label style="display:flex; align-items:center; gap:.5rem; cursor:pointer;">
                            <input type="radio" name="tipo" value="repuesto" onchange="toggleTipo()" checked> Repuesto
                        </label>
                        <label style="display:flex; align-items:center; gap:.5rem; cursor:pointer;">
                            <input type="radio" name="tipo" value="mano_obra" onchange="toggleTipo()"> Mano de Obra
                        </label>
                    </div>
                </div>

                {{-- Campos repuesto --}}
                <div id="campos-repuesto" style="display:contents;">
                    <div class="field-group">
                        <label for="id_repuesto">Repuesto <span class="req">*</span></label>
                        <select id="id_repuesto" name="id_repuesto">
                            <option value="">Seleccionar...</option>
                            @foreach($repuestos as $r)
                            <option value="{{ $r->id }}">{{ $r->nombre }} ({{ $r->marca }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field-group">
                        <label for="precio_unitario">Precio Unitario <span class="req">*</span></label>
                        <input id="precio_unitario" name="precio_unitario" type="number" step="0.01" min="0" placeholder="0.00" />
                    </div>
                    <div class="field-group">
                        <label for="cantidad_rep">Cantidad <span class="req">*</span></label>
                        <input id="cantidad_rep" name="cantidad" type="number" min="1" placeholder="1" />
                    </div>
                    <div class="field-group">
                        <label for="descuento">Descuento (%)</label>
                        <input id="descuento" name="descuento" type="number" step="0.01" min="0" max="100" placeholder="0" />
                    </div>
                </div>

                {{-- Campos mano de obra --}}
                <div id="campos-mo" style="display:none; grid-column:1 / -1;">
                    <div class="form-grid">
                        <div class="field-group">
                            <label for="id_mano_obra">Servicio <span class="req">*</span></label>
                            <select id="id_mano_obra" name="id_mano_obra">
                                <option value="">Seleccionar...</option>
                                @foreach($servicios as $s)
                                <option value="{{ $s->id }}">{{ $s->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field-group">
                            <label for="costo">Costo <span class="req">*</span></label>
                            <input id="costo" name="costo" type="number" step="0.01" min="0" placeholder="0.00" />
                        </div>
                        <div class="field-group">
                            <label for="cantidad_mo">Cantidad <span class="req">*</span></label>
                            <input id="cantidad_mo" type="number" min="1" placeholder="1" />
                        </div>
                        <div class="field-group">
                            <label for="estado_mo">Estado</label>
                            <select id="estado_mo" name="estado">
                                <option value="Pendiente">Pendiente</option>
                                <option value="En Proceso">En Proceso</option>
                                <option value="Completado">Completado</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Registrar</button>
            </div>
        </form>
    </div>
    @endif
</div>

{{-- Modal editar repuesto --}}
<div id="modal-rep" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:500; align-items:center; justify-content:center;">
    <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:2rem; width:100%; max-width:460px; margin:1rem;">
        <h3 style="font-family:'Barlow Condensed',sans-serif; font-size:1.2rem; font-weight:700; margin-bottom:1.25rem;">Editar Repuesto</h3>
        <form id="form-edit-rep" method="POST">
            @csrf @method('PUT')
            <div class="form-grid">
                <div class="field-group">
                    <label>Cantidad</label>
                    <input id="edit_rep_cantidad" name="cantidad" type="number" min="1" />
                </div>
                <div class="field-group">
                    <label>Precio Unitario</label>
                    <input id="edit_rep_precio" name="precio_unitario" type="number" step="0.01" min="0" />
                </div>
                <div class="field-group" style="grid-column:1 / -1;">
                    <label>Descuento (%)</label>
                    <input id="edit_rep_descuento" name="descuento" type="number" step="0.01" min="0" max="100" />
                </div>
            </div>
            <div style="display:flex; gap:.75rem; justify-content:flex-end; margin-top:1.25rem;">
                <button type="button" onclick="cerrarModal('modal-rep')" class="btn btn-ghost" style="color:var(--muted);">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal editar mano de obra --}}
<div id="modal-mo" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:500; align-items:center; justify-content:center;">
    <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:2rem; width:100%; max-width:460px; margin:1rem;">
        <h3 style="font-family:'Barlow Condensed',sans-serif; font-size:1.2rem; font-weight:700; margin-bottom:1.25rem;">Editar Mano de Obra</h3>
        <form id="form-edit-mo" method="POST">
            @csrf @method('PUT')
            <div class="form-grid">
                <div class="field-group">
                    <label>Cantidad</label>
                    <input id="edit_mo_cantidad" name="cantidad" type="number" min="1" />
                </div>
                <div class="field-group">
                    <label>Costo</label>
                    <input id="edit_mo_costo" name="costo" type="number" step="0.01" min="0" />
                </div>
                <div class="field-group" style="grid-column:1 / -1;">
                    <label>Estado</label>
                    <select id="edit_mo_estado" name="estado">
                        <option value="Pendiente">Pendiente</option>
                        <option value="En Proceso">En Proceso</option>
                        <option value="Completado">Completado</option>
                    </select>
                </div>
            </div>
            <div style="display:flex; gap:.75rem; justify-content:flex-end; margin-top:1.25rem;">
                <button type="button" onclick="cerrarModal('modal-mo')" class="btn btn-ghost" style="color:var(--muted);">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function toggleTipo() {
    const tipo = document.querySelector('input[name="tipo"]:checked').value;
    const camposRep = document.getElementById('campos-repuesto');
    const camposMO = document.getElementById('campos-mo');
    if (tipo === 'repuesto') {
        camposRep.style.display = 'contents';
        camposMO.style.display = 'none';
    } else {
        camposRep.style.display = 'none';
        camposMO.style.display = 'block';
    }
}

document.getElementById('form-detalle').addEventListener('submit', function() {
    const tipo = document.querySelector('input[name="tipo"]:checked').value;
    if (tipo === 'mano_obra') {
        document.querySelector('[name="cantidad"]').value = document.getElementById('cantidad_mo').value;
    }
});

function abrirEditarRep(id, cantidad, precio, descuento) {
    const form = document.getElementById('form-edit-rep');
    form.action = `/ordenes/{{ $orden->nro }}/detalles/repuesto/${id}`;
    document.getElementById('edit_rep_cantidad').value = cantidad;
    document.getElementById('edit_rep_precio').value = precio;
    document.getElementById('edit_rep_descuento').value = descuento;
    document.getElementById('modal-rep').style.display = 'flex';
}

function abrirEditarMO(id, cantidad, costo, estado) {
    const form = document.getElementById('form-edit-mo');
    form.action = `/ordenes/{{ $orden->nro }}/detalles/mano_obra/${id}`;
    document.getElementById('edit_mo_cantidad').value = cantidad;
    document.getElementById('edit_mo_costo').value = costo;
    document.getElementById('edit_mo_estado').value = estado;
    document.getElementById('modal-mo').style.display = 'flex';
}

function cerrarModal(id) {
    document.getElementById(id).style.display = 'none';
}
</script>
@endpush