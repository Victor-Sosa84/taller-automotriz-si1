@extends('layouts.app')
@section('title', 'Editar Proforma #' . $proforma->nro)
@section('content')
<div style="max-width:900px;">
    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('proforma.show', $proforma->nro) }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">← Volver a proforma</a>
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; margin-top:.5rem;">Editar Proforma #{{ $proforma->nro }}</h2>
    </div>

    @if($errors->any())
        <div class="form-errors">
            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('proforma.update', $proforma->nro) }}" method="POST" class="form-card">
        @csrf @method('PUT')

        <div class="form-grid" style="margin-bottom:1.5rem;">
            <div class="field-group">
                <label>CI Cliente</label>
                <input type="text" value="{{ $proforma->ci_cliente }}" readonly
                    style="width:100%; box-sizing:border-box; opacity:.6;">
            </div>
            <div class="field-group">
                <label for="plazo">Plazo de validez</label>
                <input type="date" id="plazo" name="plazo"
                    value="{{ old('plazo', $proforma->plazo) }}"
                    min="{{ now()->toDateString() }}"
                    style="width:100%; box-sizing:border-box;">
            </div>
        </div>

        {{-- Repuestos --}}
        <div style="margin-bottom:1.5rem;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:.75rem;">
                <h3 style="font-family:'Barlow Condensed',sans-serif; font-size:1.1rem; font-weight:700; text-transform:uppercase;">Repuestos</h3>
                <button type="button" class="btn btn-ghost btn-sm" id="add-repuesto">+ Agregar repuesto</button>
            </div>
            {{-- Header de columnas --}}
            <div style="display:grid; grid-template-columns:2fr 1fr 1fr 1fr auto; gap:.5rem; padding:0 .25rem; margin-bottom:.25rem;">
                <span style="font-size:.7rem; color:var(--muted); text-transform:uppercase; letter-spacing:.06em;">Repuesto</span>
                <span style="font-size:.7rem; color:var(--muted); text-transform:uppercase; letter-spacing:.06em;">Cantidad</span>
                <span style="font-size:.7rem; color:var(--muted); text-transform:uppercase; letter-spacing:.06em;">Precio Bs</span>
                <span style="font-size:.7rem; color:var(--muted); text-transform:uppercase; letter-spacing:.06em;">Desc. %</span>
                <span></span>
            </div>
            <div id="repuestos-container" style="display:flex; flex-direction:column; gap:.75rem;">
                {{-- filas dinámicas --}}
            </div>
            <div id="repuestos-empty" style="color:var(--muted); font-size:.85rem; padding:.5rem 0; display:none;">Sin repuestos.</div>
        </div>

        <div style="border-top:1px solid var(--border); margin:1rem 0;"></div>

        {{-- Servicios --}}
        <div style="margin-bottom:1.5rem;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:.75rem;">
                <h3 style="font-family:'Barlow Condensed',sans-serif; font-size:1.1rem; font-weight:700; text-transform:uppercase;">Servicios / Mano de Obra</h3>
                <button type="button" class="btn btn-ghost btn-sm" id="add-servicio">+ Agregar servicio</button>
            </div>
            {{-- Header de columnas --}}
            <div style="display:grid; grid-template-columns:2fr 1fr 1fr auto; gap:.5rem; padding:0 .25rem; margin-bottom:.25rem;">
                <span style="font-size:.7rem; color:var(--muted); text-transform:uppercase; letter-spacing:.06em;">Servicio</span>
                <span style="font-size:.7rem; color:var(--muted); text-transform:uppercase; letter-spacing:.06em;">Cantidad</span>
                <span style="font-size:.7rem; color:var(--muted); text-transform:uppercase; letter-spacing:.06em;">Costo Bs</span>
                <span></span>
            </div>
            <div id="servicios-container" style="display:flex; flex-direction:column; gap:.75rem;">
                {{-- filas dinámicas --}}
            </div>
            <div id="servicios-empty" style="color:var(--muted); font-size:.85rem; padding:.5rem 0; display:none;">Sin servicios.</div>
        </div>

        <div style="border-top:1px solid var(--border); margin:1rem 0;"></div>

        <div style="display:flex; justify-content:flex-end; margin-bottom:1rem;">
            <div style="text-align:right;">
                <div style="font-size:.8rem; color:var(--muted); text-transform:uppercase; letter-spacing:.06em;">Total estimado</div>
                <div id="total-preview" style="font-family:'Barlow Condensed',sans-serif; font-size:2rem; font-weight:800; color:var(--accent);">Bs 0.00</div>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('proforma.show', $proforma->nro) }}" class="btn btn-ghost">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar Proforma</button>
        </div>
    </form>
</div>

<script>
const REPUESTOS = @json($repuestos);
const SERVICIOS = @json($servicios);
const REPUESTOS_EXISTENTES = @json($repuestosExistentes);
const SERVICIOS_EXISTENTES = @json($serviciosExistentes);
</script>
@endsection

@push('scripts')
<script>
let repuestoIdx = 0;
let servicioIdx = 0;

function actualizarTotal() {
    let total = 0;
    document.querySelectorAll('.fila-repuesto').forEach(fila => {
        const precio = parseFloat(fila.querySelector('.r-precio').value) || 0;
        const cant   = parseInt(fila.querySelector('.r-cantidad').value) || 0;
        const desc   = parseFloat(fila.querySelector('.r-descuento').value) || 0;
        total += (precio * cant) * (1 - desc / 100);
    });
    document.querySelectorAll('.fila-servicio').forEach(fila => {
        const costo = parseFloat(fila.querySelector('.s-costo').value) || 0;
        const cant  = parseInt(fila.querySelector('.s-cantidad').value) || 0;
        total += costo * cant;
    });
    document.getElementById('total-preview').textContent = 'Bs ' + total.toFixed(2);
}

function crearFilaRepuesto(idx, data = {}) {
    const div = document.createElement('div');
    div.className = 'fila-repuesto';
    div.style.cssText = 'display:grid; grid-template-columns:2fr 1fr 1fr 1fr auto; gap:.5rem; align-items:center;';
    div.innerHTML = `
        <select name="repuestos[${idx}][id_repuesto]" style="width:100%; box-sizing:border-box; background:var(--surface2); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); padding:.6rem .8rem;">
            <option value="">Seleccionar repuesto...</option>
            ${REPUESTOS.map(r => `<option value="${r.id}" ${data.id_repuesto == r.id ? 'selected' : ''}>${r.nombre}${r.marca ? ' — '+r.marca : ''}</option>`).join('')}
        </select>
        <input type="number" name="repuestos[${idx}][cantidad]" class="r-cantidad" min="1" value="${data.cantidad ?? 1}"
            style="width:100%; box-sizing:border-box; background:var(--surface2); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); padding:.6rem .8rem;">
        <input type="number" name="repuestos[${idx}][precio]" class="r-precio" min="0" step="0.01" value="${data.precio ?? 0}"
            style="width:100%; box-sizing:border-box; background:var(--surface2); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); padding:.6rem .8rem;">
        <input type="number" name="repuestos[${idx}][descuento]" class="r-descuento" min="0" max="100" step="0.01" value="${data.descuento ?? 0}"
            style="width:100%; box-sizing:border-box; background:var(--surface2); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); padding:.6rem .8rem;">
        <button type="button" class="btn btn-danger btn-sm quitar-repuesto">✕</button>
    `;
    div.querySelectorAll('input').forEach(i => i.addEventListener('input', actualizarTotal));
    div.querySelector('.quitar-repuesto').addEventListener('click', () => {
        div.remove(); actualizarTotal(); toggleEmpty('repuestos');
    });
    return div;
}

function crearFilaServicio(idx, data = {}) {
    const div = document.createElement('div');
    div.className = 'fila-servicio';
    div.style.cssText = 'display:grid; grid-template-columns:2fr 1fr 1fr auto; gap:.5rem; align-items:center;';
    div.innerHTML = `
        <select name="servicios[${idx}][id_servicio]" style="width:100%; box-sizing:border-box; background:var(--surface2); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); padding:.6rem .8rem;">
            <option value="">Seleccionar servicio...</option>
            ${SERVICIOS.map(s => `<option value="${s.id}" ${data.id_servicio == s.id ? 'selected' : ''}>${s.descripcion}</option>`).join('')}
        </select>
        <input type="number" name="servicios[${idx}][cantidad]" class="s-cantidad" min="1" value="${data.cantidad ?? 1}"
            style="width:100%; box-sizing:border-box; background:var(--surface2); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); padding:.6rem .8rem;">
        <input type="number" name="servicios[${idx}][costo]" class="s-costo" min="0" step="0.01" value="${data.costo ?? 0}"
            style="width:100%; box-sizing:border-box; background:var(--surface2); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); padding:.6rem .8rem;">
        <button type="button" class="btn btn-danger btn-sm quitar-servicio">✕</button>
    `;
    div.querySelectorAll('input').forEach(i => i.addEventListener('input', actualizarTotal));
    div.querySelector('.quitar-servicio').addEventListener('click', () => {
        div.remove(); actualizarTotal(); toggleEmpty('servicios');
    });
    return div;
}

function toggleEmpty(tipo) {
    const container = document.getElementById(`${tipo}-container`);
    const empty     = document.getElementById(`${tipo}-empty`);
    empty.style.display = container.children.length === 0 ? 'block' : 'none';
}

// Pre-cargar existentes
REPUESTOS_EXISTENTES.forEach(r => {
    document.getElementById('repuestos-container').appendChild(crearFilaRepuesto(repuestoIdx++, r));
});
SERVICIOS_EXISTENTES.forEach(s => {
    document.getElementById('servicios-container').appendChild(crearFilaServicio(servicioIdx++, s));
});
toggleEmpty('repuestos');
toggleEmpty('servicios');
actualizarTotal();

document.getElementById('add-repuesto').addEventListener('click', () => {
    document.getElementById('repuestos-container').appendChild(crearFilaRepuesto(repuestoIdx++));
    toggleEmpty('repuestos'); actualizarTotal();
});
document.getElementById('add-servicio').addEventListener('click', () => {
    document.getElementById('servicios-container').appendChild(crearFilaServicio(servicioIdx++));
    toggleEmpty('servicios'); actualizarTotal();
});
</script>
@endpush