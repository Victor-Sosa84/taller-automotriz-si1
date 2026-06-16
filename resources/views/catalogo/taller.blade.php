@extends('layouts.app')
@section('title', 'Catálogo — Taller')

@section('content')
<div style="max-width:1000px;">
    <div style="margin-bottom:1.5rem;">
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; margin-top:.5rem;">Catálogo — Taller</h2>
        <p style="color:var(--muted); font-size:.95rem; margin-top:.25rem;">Gestión de repuestos, mano de obra, herramientas, tipos y marcas del sistema.</p>
    </div>

    {{-- REPUESTOS --}}
    <div class="table-wrap" style="margin-bottom:2rem;">
        <div style="padding:.75rem 1rem; background:var(--surface2); border-bottom:1px solid var(--border);">
            <span style="font-family:'Barlow Condensed',sans-serif; font-size:.85rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--muted);">Repuestos</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Marca</th>
                    <th>Estado</th>
                    <th>Precio Ref.</th>
                    <th style="text-align:center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($repuestos as $r)
                <tr style="vertical-align:middle;" id="rep-row-{{ $r->id }}">
                    <td class="td-muted">{{ $r->id }}</td>
                    <td id="rep-nombre-{{ $r->id }}">{{ $r->nombre }}</td>
                    <td id="rep-marca-{{ $r->id }}">{{ $r->marca ?? '—' }}</td>
                    <td id="rep-estado-{{ $r->id }}">{{ $r->estado ?? '—' }}</td>
                    <td id="rep-precio-{{ $r->id }}">{{ $r->precio_referencial ? 'Bs '.number_format($r->precio_referencial, 2) : '—' }}</td>
                    <td style="text-align:center; white-space:nowrap;">
                        <button onclick="editarRepuesto({{ $r->id }}, '{{ $r->nombre }}', '{{ $r->marca }}', '{{ $r->estado }}', '{{ $r->precio_referencial }}')"
                            class="btn btn-sm btn-ghost">Editar</button>
                        <form method="POST" action="{{ route('catalogo.repuesto.destroy', $r->id) }}" style="display:inline;"
                            onsubmit="return confirm('¿Eliminar este repuesto?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center; color:var(--muted); padding:1.5rem;">Sin repuestos registrados.</td></tr>
                @endforelse
                {{-- Fila formulario agregar --}}
                <tr style="background:var(--surface2);">
                    <form method="POST" action="{{ route('catalogo.repuesto.store') }}" style="display:contents;">
                        @csrf
                        <td class="td-muted">+</td>
                        <td><input name="nombre" type="text" placeholder="Nombre" style="width:100%; background:var(--bg); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); padding:.4rem .6rem; font-size:.85rem;" required /></td>
                        <td><input name="marca" type="text" placeholder="Marca" style="width:100%; background:var(--bg); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); padding:.4rem .6rem; font-size:.85rem;" /></td>
                        <td>
                            <select name="estado" style="width:100%; background:var(--bg); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); padding:.4rem .6rem; font-size:.85rem;">
                                <option value="Disponible">Disponible</option>
                                <option value="Agotado">Agotado</option>
                            </select>
                        </td>
                        <td><input name="precio_referencial" type="number" step="0.01" min="0" placeholder="Precio Bs" style="width:100%; background:var(--bg); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); padding:.4rem .6rem; font-size:.85rem;" /></td>
                        <td style="text-align:center;"><button type="submit" class="btn btn-sm btn-primary">Agregar</button></td>
                    </form>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- MANO DE OBRA --}}
    <div class="table-wrap" style="margin-bottom:2rem;">
        <div style="padding:.75rem 1rem; background:var(--surface2); border-bottom:1px solid var(--border);">
            <span style="font-family:'Barlow Condensed',sans-serif; font-size:.85rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--muted);">Mano de Obra</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Descripción</th>
                    <th>Costo Ref.</th>
                    <th style="text-align:center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($servicios as $s)
                <tr style="vertical-align:middle;">
                    <td class="td-muted">{{ $s->id }}</td>
                    <td>{{ $s->descripcion }}</td>
                    <td>{{ $s->costo_referencial ? 'Bs '.number_format($s->costo_referencial, 2) : '—' }}</td>
                    <td style="text-align:center; white-space:nowrap;">
                        <button onclick="editarMO({{ $s->id }}, '{{ $s->descripcion }}', '{{ $s->costo_referencial }}')"
                            class="btn btn-sm btn-ghost">Editar</button>
                        <form method="POST" action="{{ route('catalogo.mo.destroy', $s->id) }}" style="display:inline;"
                            onsubmit="return confirm('¿Eliminar este servicio?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center; color:var(--muted); padding:1.5rem;">Sin servicios registrados.</td></tr>
                @endforelse
                <tr style="background:var(--surface2);">
                    <form method="POST" action="{{ route('catalogo.mo.store') }}" style="display:contents;">
                        @csrf
                        <td class="td-muted">+</td>
                        <td><input name="descripcion" type="text" placeholder="Descripción del servicio" style="width:100%; background:var(--bg); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); padding:.4rem .6rem; font-size:.85rem;" required /></td>
                        <td><input name="costo_referencial" type="number" step="0.01" min="0" placeholder="Costo Bs" style="width:100%; background:var(--bg); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); padding:.4rem .6rem; font-size:.85rem;" /></td>
                        <td style="text-align:center;"><button type="submit" class="btn btn-sm btn-primary">Agregar</button></td>
                    </form>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- HERRAMIENTAS --}}
    <div class="table-wrap" style="margin-bottom:2rem;">
        <div style="padding:.75rem 1rem; background:var(--surface2); border-bottom:1px solid var(--border);">
            <span style="font-family:'Barlow Condensed',sans-serif; font-size:.85rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--muted);">Herramientas</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Descripción</th>
                    <th>Tipo</th>
                    <th>Marca</th>
                    <th>Estado</th>
                    <th>Disponible</th>
                    <th style="text-align:center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($herramientas as $h)
                <tr style="vertical-align:middle;">
                    <td class="td-muted">{{ $h->nro }}</td>
                    <td>{{ $h->descripcion ?? '—' }}</td>
                    <td>{{ $h->tipo->descripcion ?? '—' }}</td>
                    <td>{{ $h->marca->nombre ?? '—' }}</td>
                    <td>{{ $h->estado ?? '—' }}</td>
                    <td>
                        <span style="color:{{ $h->disponible ? 'var(--success)' : 'var(--danger)' }}">
                            {{ $h->disponible ? 'Sí' : 'No' }}
                        </span>
                    </td>
                    <td style="text-align:center; white-space:nowrap;">
                        <button onclick="editarHerramienta({{ $h->nro }}, '{{ $h->descripcion }}', '{{ $h->estado }}', {{ $h->id_tipo_herramienta }}, {{ $h->id_marca_herramienta }})"
                            class="btn btn-sm btn-ghost">Editar</button>
                        <form method="POST" action="{{ route('catalogo.herramienta.destroy', $h->nro) }}" style="display:inline;"
                            onsubmit="return confirm('¿Eliminar esta herramienta?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center; color:var(--muted); padding:1.5rem;">Sin herramientas registradas.</td></tr>
                @endforelse
                <tr style="background:var(--surface2);">
                    <form method="POST" action="{{ route('catalogo.herramienta.store') }}" style="display:contents;">
                        @csrf
                        <td class="td-muted">+</td>
                        <td><input name="descripcion" type="text" placeholder="Descripción" style="width:100%; background:var(--bg); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); padding:.4rem .6rem; font-size:.85rem;" /></td>
                        <td>
                            <select name="id_tipo_herramienta" style="width:100%; background:var(--bg); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); padding:.4rem .6rem; font-size:.85rem;" required>
                                <option value="">Tipo...</option>
                                @foreach($tipos as $t)
                                <option value="{{ $t->id }}">{{ $t->descripcion }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select name="id_marca_herramienta" style="width:100%; background:var(--bg); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); padding:.4rem .6rem; font-size:.85rem;" required>
                                <option value="">Marca...</option>
                                @foreach($marcas as $m)
                                <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select name="estado" style="width:100%; background:var(--bg); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); padding:.4rem .6rem; font-size:.85rem;">
                                <option value="Bueno">Bueno</option>
                                <option value="Regular">Regular</option>
                                <option value="Malo">Malo</option>
                            </select>
                        </td>
                        <td></td>
                        <td style="text-align:center;"><button type="submit" class="btn btn-sm btn-primary">Agregar</button></td>
                    </form>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- TIPOS DE HERRAMIENTA --}}
    <div class="table-wrap" style="margin-bottom:2rem;">
        <div style="padding:.75rem 1rem; background:var(--surface2); border-bottom:1px solid var(--border);">
            <span style="font-family:'Barlow Condensed',sans-serif; font-size:.85rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--muted);">Tipos de Herramienta</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width:60px;">#</th>
                    <th>Descripción</th>
                    <th style="width:180px; text-align:center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tipos as $t)
                <tr style="vertical-align:middle;">
                    <td class="td-muted">{{ $t->id }}</td>
                    <td>{{ $t->descripcion }}</td>
                    <td style="text-align:center; white-space:nowrap;">
                        <button onclick="editarTipo({{ $t->id }}, '{{ $t->descripcion }}')" class="btn btn-sm btn-ghost">Editar</button>
                        <form method="POST" action="{{ route('catalogo.tipo.destroy', $t->id) }}" style="display:inline;" onsubmit="return confirm('¿Eliminar este tipo?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" style="text-align:center; color:var(--muted); padding:1.5rem;">Sin tipos registrados.</td></tr>
                @endforelse
                <tr style="background:var(--surface2);">
                    <form method="POST" action="{{ route('catalogo.tipo.store') }}" style="display:contents;">
                        @csrf
                        <td class="td-muted">+</td>
                        <td><input name="descripcion" type="text" placeholder="Descripción del tipo" style="width:100%; background:var(--bg); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); padding:.4rem .6rem; font-size:.85rem;" required /></td>
                        <td style="text-align:center;"><button type="submit" class="btn btn-sm btn-primary">Agregar</button></td>
                    </form>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- MARCAS DE HERRAMIENTA --}}
    <div class="table-wrap" style="margin-bottom:2rem;">
        <div style="padding:.75rem 1rem; background:var(--surface2); border-bottom:1px solid var(--border);">
            <span style="font-family:'Barlow Condensed',sans-serif; font-size:.85rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--muted);">Marcas de Herramienta</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width:60px;">#</th>
                    <th>Descripción</th>
                    <th style="width:180px; text-align:center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($marcas as $m)
                <tr style="vertical-align:middle;">
                    <td class="td-muted">{{ $m->id }}</td>
                    <td>{{ $m->nombre }}</td>
                    <td style="text-align:center; white-space:nowrap;">
                        <button onclick="editarMarca({{ $m->id }}, '{{ $m->nombre }}')" class="btn btn-sm btn-ghost">Editar</button>
                        <form method="POST" action="{{ route('catalogo.marca.destroy', $m->id) }}" style="display:inline;" onsubmit="return confirm('¿Eliminar esta marca?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" style="text-align:center; color:var(--muted); padding:1.5rem;">Sin marcas registradas.</td></tr>
                @endforelse
                <tr style="background:var(--surface2);">
                    <form method="POST" action="{{ route('catalogo.marca.store') }}" style="display:contents;">
                        @csrf
                        <td class="td-muted">+</td>
                        <td><input name="nombre" type="text" placeholder="Nombre de la marca" style="width:100%; background:var(--bg); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); padding:.4rem .6rem; font-size:.85rem;" required /></td>
                        <td style="text-align:center;"><button type="submit" class="btn btn-sm btn-primary">Agregar</button></td>
                    </form>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Modal editar repuesto --}}
<div id="modal-rep" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:500; align-items:center; justify-content:center;">
    <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:2rem; width:100%; max-width:420px; margin:1rem;">
        <h3 style="font-family:'Barlow Condensed',sans-serif; font-size:1.2rem; font-weight:700; margin-bottom:1.25rem;">Editar Repuesto</h3>
        <form id="form-rep" method="POST">
            @csrf @method('PUT')
            <div class="form-grid">
                <div class="field-group" style="grid-column:1/-1;">
                    <label>Nombre</label>
                    <input id="edit-rep-nombre" name="nombre" type="text" required />
                </div>
                <div class="field-group">
                    <label>Marca</label>
                    <input id="edit-rep-marca" name="marca" type="text" />
                </div>
                <div class="field-group">
                    <label>Estado</label>
                    <select id="edit-rep-estado" name="estado">
                        <option value="Disponible">Disponible</option>
                        <option value="Agotado">Agotado</option>
                    </select>
                </div>
                <div class="field-group">
                    <label>Precio referencial (Bs)</label>
                    <input id="edit-rep-precio" name="precio_referencial" type="number" step="0.01" min="0" />
                </div>
            </div>
            <div style="display:flex; gap:.75rem; justify-content:flex-end; margin-top:1.25rem;">
                <button type="button" onclick="cerrarModal('modal-rep')" class="btn btn-ghost" style="color:var(--muted);">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal editar MO --}}
<div id="modal-mo" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:500; align-items:center; justify-content:center;">
    <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:2rem; width:100%; max-width:420px; margin:1rem;">
        <h3 style="font-family:'Barlow Condensed',sans-serif; font-size:1.2rem; font-weight:700; margin-bottom:1.25rem;">Editar Mano de Obra</h3>
        <form id="form-mo" method="POST">
            @csrf @method('PUT')
            <div class="field-group">
                <label>Descripción</label>
                <input id="edit-mo-desc" name="descripcion" type="text" required />
            </div>
            <div class="field-group">
                <label>Costo referencial (Bs)</label>
                <input id="edit-mo-costo" name="costo_referencial" type="number" step="0.01" min="0" />
            </div>
            <div style="display:flex; gap:.75rem; justify-content:flex-end; margin-top:1.25rem;">
                <button type="button" onclick="cerrarModal('modal-mo')" class="btn btn-ghost" style="color:var(--muted);">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal editar herramienta --}}
<div id="modal-herr" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:500; align-items:center; justify-content:center;">
    <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:2rem; width:100%; max-width:420px; margin:1rem;">
        <h3 style="font-family:'Barlow Condensed',sans-serif; font-size:1.2rem; font-weight:700; margin-bottom:1.25rem;">Editar Herramienta</h3>
        <form id="form-herr" method="POST">
            @csrf @method('PUT')
            <div class="form-grid">
                <div class="field-group" style="grid-column:1/-1;">
                    <label>Descripción</label>
                    <input id="edit-herr-desc" name="descripcion" type="text" />
                </div>
                <div class="field-group" style="grid-column:1/-1;">
                    <label>Estado</label>
                    <select id="edit-herr-estado" name="estado">
                        <option value="Bueno">Bueno</option>
                        <option value="Regular">Regular</option>
                        <option value="Malo">Malo</option>
                    </select>
                </div>
                <div class="field-group" style="grid-column:1/-1;">
                    <label>Tipo</label>
                    <select id="edit-herr-tipo" name="id_tipo_herramienta">
                        @foreach($tipos as $t)
                        <option value="{{ $t->id }}">{{ $t->descripcion }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field-group" style="grid-column:1/-1;">
                    <label>Marca</label>
                    <select id="edit-herr-marca" name="id_marca_herramienta">
                        @foreach($marcas as $m)
                        <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="display:flex; gap:.75rem; justify-content:flex-end; margin-top:1.25rem;">
                <button type="button" onclick="cerrarModal('modal-herr')" class="btn btn-ghost" style="color:var(--muted);">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal editar tipo --}}
<div id="modal-tipo" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:500; align-items:center; justify-content:center;">
    <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:2rem; width:100%; max-width:420px; margin:1rem;">
        <h3 style="font-family:'Barlow Condensed',sans-serif; font-size:1.2rem; font-weight:700; margin-bottom:1.25rem;">Editar Tipo</h3>
        <form id="form-tipo" method="POST">
            @csrf @method('PUT')
            <div class="field-group">
                <label>Descripción</label>
                <input id="edit-tipo-desc" name="descripcion" type="text" required />
            </div>
            <div style="display:flex; gap:.75rem; justify-content:flex-end; margin-top:1.25rem;">
                <button type="button" onclick="cerrarModal('modal-tipo')" class="btn btn-ghost" style="color:var(--muted);">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal editar marca --}}
<div id="modal-marca" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:500; align-items:center; justify-content:center;">
    <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:2rem; width:100%; max-width:420px; margin:1rem;">
        <h3 style="font-family:'Barlow Condensed',sans-serif; font-size:1.2rem; font-weight:700; margin-bottom:1.25rem;">Editar Marca</h3>
        <form id="form-marca" method="POST">
            @csrf @method('PUT')
            <div class="field-group">
                <label>Nombre</label>
                <input id="edit-marca-nombre" name="nombre" type="text" required />
            </div>
            <div style="display:flex; gap:.75rem; justify-content:flex-end; margin-top:1.25rem;">
                <button type="button" onclick="cerrarModal('modal-marca')" class="btn btn-ghost" style="color:var(--muted);">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function editarRepuesto(id, nombre, marca, estado, precio) {
    document.getElementById('form-rep').action = `/catalogos/repuestos/${id}`;
    document.getElementById('edit-rep-nombre').value = nombre;
    document.getElementById('edit-rep-marca').value = marca !== 'null' ? marca : '';
    document.getElementById('edit-rep-estado').value = estado !== 'null' ? estado : 'Disponible';
    document.getElementById('edit-rep-precio').value = precio !== 'null' ? precio : '';
    document.getElementById('modal-rep').style.display = 'flex';
}
function editarMO(id, desc, costo) {
    document.getElementById('form-mo').action = `/catalogos/mano-obra/${id}`;
    document.getElementById('edit-mo-desc').value = desc;
    document.getElementById('edit-mo-costo').value = costo !== 'null' ? costo : '';
    document.getElementById('modal-mo').style.display = 'flex';
}
function editarHerramienta(nro, desc, estado, idTipo, idMarca) {
    document.getElementById('form-herr').action = `/catalogos/herramientas/${nro}`;
    document.getElementById('edit-herr-desc').value = desc !== 'null' ? desc : '';
    document.getElementById('edit-herr-estado').value = estado !== 'null' ? estado : 'Bueno';
    document.getElementById('edit-herr-tipo').value = idTipo;
    document.getElementById('edit-herr-marca').value = idMarca;
    document.getElementById('modal-herr').style.display = 'flex';
}
function editarTipo(id, desc) {
    document.getElementById('form-tipo').action = `/catalogos/tipos/${id}`;
    document.getElementById('edit-tipo-desc').value = desc;
    document.getElementById('modal-tipo').style.display = 'flex';
}
function editarMarca(id, nombre) {
    document.getElementById('form-marca').action = `/catalogos/marcas/${id}`;
    document.getElementById('edit-marca-nombre').value = nombre;
    document.getElementById('modal-marca').style.display = 'flex';
}
function cerrarModal(id) {
    document.getElementById(id).style.display = 'none';
}
</script>
@endpush