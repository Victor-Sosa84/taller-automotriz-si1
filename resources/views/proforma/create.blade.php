@extends('layouts.app')
@section('title', 'Elaborar Proforma')
@section('content')
<div style="max-width:900px;">
    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('diagnostico.show', $diagnostico->id) }}" 
            style="font-size:.8rem; color:var(--muted); text-decoration:none;">← Volver</a>
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; margin-top:.5rem;">Elaborar Proforma</h2>
        <p style="color:var(--muted); font-size:.95rem; margin-top:.25rem;">
            Diagnóstico #{{ $diagnostico->id }} — Vehículo {{ $diagnostico->auto->placa ?? '—' }}
        </p>
    </div>

    @if($errors->any())
        <div class="form-errors">
            <strong>Por favor corrige los siguientes campos:</strong>
            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('proforma.store') }}" method="POST" id="proforma-form" class="form-card">
        @csrf
        <input type="hidden" name="diagnostico_id" value="{{ $diagnostico->id }}">

        {{-- Plazo --}}
        <div class="form-grid" style="margin-bottom:1.5rem;">
            <div class="field-group">
                <label for="ci_cliente">CI Cliente <span class="req">*</span></label>
                <div style="position:relative;">
                    <input type="text"
                        id="ci-input"
                        name="ci_cliente"
                        placeholder="Ej. 8512347"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        maxlength="8"
                        required
                        value="{{ old('ci_cliente') }}"
                        oninput="buscarClientePorCI(this.value)"
                        autocomplete="off"
                        style="width:100%; box-sizing:border-box;">
                    <span id="ci-status" style="position:absolute; right:.75rem; top:50%;
                        transform:translateY(-50%); font-size:.72rem; color:var(--muted); display:none;">
                        buscando...
                    </span>
                </div>
                <div id="cliente-encontrado" style="display:none; margin-top:.4rem;
                    background:rgba(46,204,113,.08); border:1px solid rgba(46,204,113,.25);
                    border-radius:4px; padding:.5rem .75rem; font-size:.78rem; color:var(--success);">
                    ✓ Cliente: <span id="cliente-nombre" style="font-weight:600;"></span>
                </div>
                <div id="cliente-no-encontrado" style="display:none; margin-top:.4rem;
                    background:rgba(231,76,60,.08); border:1px solid rgba(231,76,60,.25);
                    border-radius:4px; padding:.5rem .75rem; font-size:.78rem; color:var(--danger);">
                    ✗ CI no encontrado — el cliente debe estar registrado.
                </div>
            </div>
            <div class="field-group">
                <label for="plazo">Plazo estimado</label>
                <input type="date" id="plazo" name="plazo" value="{{ old('plazo') }}"
                    style="width:100%; box-sizing:border-box;">
            </div>
        </div>

        {{-- REPUESTOS --}}
        <div style="margin-bottom:1.5rem;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:.75rem;">
                <h3 style="font-family:'Barlow Condensed',sans-serif; font-size:1.1rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Repuestos</h3>
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
            <div id="repuestos-empty" style="color:var(--muted); font-size:.85rem; padding:.5rem 0;">
                Sin repuestos agregados.
            </div>
        </div>

        <div style="border-top:1px solid var(--border); margin:1rem 0;"></div>

        {{-- SERVICIOS --}}
        <div style="margin-bottom:1.5rem;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:.75rem;">
                <h3 style="font-family:'Barlow Condensed',sans-serif; font-size:1.1rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Servicios / Mano de Obra</h3>
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
            <div id="servicios-empty" style="color:var(--muted); font-size:.85rem; padding:.5rem 0;">
                Sin servicios agregados.
            </div>
        </div>

        <div style="border-top:1px solid var(--border); margin:1rem 0;"></div>

        {{-- TOTAL PREVIEW --}}
        <div style="display:flex; justify-content:flex-end; margin-bottom:1rem;">
            <div style="text-align:right;">
                <div style="font-size:.8rem; color:var(--muted); text-transform:uppercase; letter-spacing:.06em;">Total estimado</div>
                <div id="total-preview" style="font-family:'Barlow Condensed',sans-serif; font-size:2rem; font-weight:800; color:var(--accent);">Bs 0.00</div>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('diagnostico.show', $diagnostico->id) }}" class="btn btn-ghost">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Proforma</button>
        </div>
    </form>
</div>

{{-- Templates de datos para JS --}}
<script>
const REPUESTOS = @json($repuestos);
const SERVICIOS = @json($servicios);
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

function crearFilaRepuesto(idx) {
    const div = document.createElement('div');
    div.className = 'fila-repuesto';
    div.style.cssText = 'display:grid; grid-template-columns:2fr 1fr 1fr 1fr auto; gap:.5rem; align-items:center;';
    div.innerHTML = `
        <select name="repuestos[${idx}][id_repuesto]" style="width:100%; box-sizing:border-box; background:var(--surface2); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); padding:.6rem .8rem;">
            <option value="">Seleccionar repuesto...</option>
            ${REPUESTOS.map(r => `<option value="${r.id}">${r.nombre}${r.marca ? ' — '+r.marca : ''}</option>`).join('')}
        </select>
        <input type="number" name="repuestos[${idx}][cantidad]" class="r-cantidad" min="1" value="1"
            placeholder="Cant." style="width:100%; box-sizing:border-box; background:var(--surface2); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); padding:.6rem .8rem;">
        <input type="number" name="repuestos[${idx}][precio]" class="r-precio" min="0" step="0.01" value="0"
            placeholder="Precio Bs" style="width:100%; box-sizing:border-box; background:var(--surface2); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); padding:.6rem .8rem;">
        <input type="number" name="repuestos[${idx}][descuento]" class="r-descuento" min="0" max="100" step="0.01" value="0"
            placeholder="Desc. %" style="width:100%; box-sizing:border-box; background:var(--surface2); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); padding:.6rem .8rem;">
        <button type="button" class="btn btn-danger btn-sm quitar-repuesto">✕</button>
    `;
    div.querySelectorAll('input').forEach(i => i.addEventListener('input', actualizarTotal));
    div.querySelector('.quitar-repuesto').addEventListener('click', () => {
        div.remove();
        actualizarTotal();
        toggleEmpty('repuestos');
    });
    return div;
}

function crearFilaServicio(idx) {
    const div = document.createElement('div');
    div.className = 'fila-servicio';
    div.style.cssText = 'display:grid; grid-template-columns:2fr 1fr 1fr auto; gap:.5rem; align-items:center;';
    div.innerHTML = `
        <select name="servicios[${idx}][id_servicio]" style="width:100%; box-sizing:border-box; background:var(--surface2); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); padding:.6rem .8rem;">
            <option value="">Seleccionar servicio...</option>
            ${SERVICIOS.map(s => `<option value="${s.id}">${s.descripcion}</option>`).join('')}
        </select>
        <input type="number" name="servicios[${idx}][cantidad]" class="s-cantidad" min="1" value="1"
            placeholder="Cant." style="width:100%; box-sizing:border-box; background:var(--surface2); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); padding:.6rem .8rem;">
        <input type="number" name="servicios[${idx}][costo]" class="s-costo" min="0" step="0.01" value="0"
            placeholder="Costo Bs" style="width:100%; box-sizing:border-box; background:var(--surface2); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); padding:.6rem .8rem;">
        <button type="button" class="btn btn-danger btn-sm quitar-servicio">✕</button>
    `;
    div.querySelectorAll('input').forEach(i => i.addEventListener('input', actualizarTotal));
    div.querySelector('.quitar-servicio').addEventListener('click', () => {
        div.remove();
        actualizarTotal();
        toggleEmpty('servicios');
    });
    return div;
}

function toggleEmpty(tipo) {
    const container = document.getElementById(`${tipo}-container`);
    const empty     = document.getElementById(`${tipo}-empty`);
    empty.style.display = container.children.length === 0 ? 'block' : 'none';
}

document.getElementById('add-repuesto').addEventListener('click', () => {
    document.getElementById('repuestos-container').appendChild(crearFilaRepuesto(repuestoIdx++));
    toggleEmpty('repuestos');
    actualizarTotal();
});

document.getElementById('add-servicio').addEventListener('click', () => {
    document.getElementById('servicios-container').appendChild(crearFilaServicio(servicioIdx++));
    toggleEmpty('servicios');
    actualizarTotal();
});

let _ciTimer = null;

function buscarClientePorCI(ci) {
    const soloNumeros = ci.replace(/\D/g, '');
    if (soloNumeros !== ci) {
        document.getElementById('ci-input').value = soloNumeros;
        return;
    }

    clearTimeout(_ciTimer);
    const status      = document.getElementById('ci-status');
    const encontrado  = document.getElementById('cliente-encontrado');
    const noEncontrado = document.getElementById('cliente-no-encontrado');
    const nombre      = document.getElementById('cliente-nombre');

    encontrado.style.display   = 'none';
    noEncontrado.style.display = 'none';

    if (ci.length < 4) {
        status.style.display = 'none';
        return;
    }

    status.style.display = 'inline';
    status.textContent   = 'buscando...';

    _ciTimer = setTimeout(async () => {
        try {
            const res  = await fetch('/api/persona/' + encodeURIComponent(ci), {
                headers: { 'Accept': 'application/json' }
            });
            const text = await res.text();
            status.style.display = 'none';

            if (!text || text === 'null') {
                noEncontrado.style.display = 'block';
                return;
            }

            const data = JSON.parse(text);

            if (data && data.ci && data.es_cliente) {
                nombre.textContent        = data.nombre || ci;
                encontrado.style.display  = 'block';
                noEncontrado.style.display = 'none';
            } else {
                noEncontrado.style.display = 'block';
                encontrado.style.display   = 'none';
            }
        } catch (e) {
            status.style.display = 'none';
        }
    }, 500);
}
</script>
@endpush