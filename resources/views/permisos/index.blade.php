@extends('layouts.app')
@section('title', 'Gestión de Privilegios')

@section('content')

<div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem; margin-bottom:1.5rem;">
    <div>
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; text-transform:uppercase;">
            Gestión de Privilegios
        </h2>
        <p style="color:var(--muted); font-size:.85rem; margin-top:.2rem;">
            Activa o desactiva privilegios por perfil — por paquete, caso de uso o de forma individual.
        </p>
    </div>
</div>

{{-- Tabs de roles --}}
<div style="display:flex; gap:.5rem; margin-bottom:1.75rem; flex-wrap:wrap;">
    @foreach($roles as $rol)
    <button onclick="mostrarRol({{ $rol->id }})"
            id="tab-{{ $rol->id }}"
            class="btn {{ $loop->first ? 'btn-primary' : 'btn-ghost' }}"
            style="font-family:'Barlow Condensed',sans-serif; font-weight:700; letter-spacing:.05em;">
        👤 {{ $rol->nombre }}
    </button>
    @endforeach
</div>

{{-- Panel por rol --}}
@foreach($roles as $rol)
@php
    $activosIds = $rol->permisos
        ->filter(fn($p) => $p->pivot->estado === 'Activo')
        ->pluck('id')->toArray();
@endphp

<div id="panel-{{ $rol->id }}" style="{{ $loop->first ? '' : 'display:none;' }}">

    {{-- Header del rol --}}
    <div style="background:var(--surface2); border:1px solid var(--border); border-radius:var(--radius);
                padding:1rem 1.25rem; margin-bottom:1.25rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem;">
        <div>
            <span style="font-family:'Barlow Condensed',sans-serif; font-size:1.1rem; font-weight:800; text-transform:uppercase;">
                {{ $rol->nombre }}
            </span>
            <span id="contador-global-{{ $rol->id }}" style="color:var(--muted); font-size:.8rem; margin-left:.75rem;">
                {{ count($activosIds) }} privilegio(s) activo(s) de {{ $permisos->flatten()->flatten()->count() }}
            </span>
        </div>
        <span style="font-size:.78rem; color:var(--muted);">{{ $rol->descripcion }}</span>
    </div>

    {{-- PAQUETES (accordion vertical) --}}
    @foreach($permisos as $paquete => $casosDeUso)
    @php
        $idsEnPaquete   = $casosDeUso->flatten()->pluck('id')->toArray();
        $activosPaquete = count(array_intersect($idsEnPaquete, $activosIds));
        $totalPaquete   = count($idsEnPaquete);
        $todosActivos   = $activosPaquete === $totalPaquete;
        $slugPaquete    = \Str::slug($paquete);
    @endphp

    <div class="card" style="margin-bottom:.75rem; overflow:hidden;">

        {{-- Header del paquete --}}
        <div style="display:flex; align-items:center; justify-content:space-between; padding:.9rem 1.25rem;
                    cursor:pointer; user-select:none; background:var(--surface2);"
             onclick="toggleAccordion('paq-{{ $rol->id }}-{{ $slugPaquete }}')">

            <div style="display:flex; align-items:center; gap:.75rem;">
                <span id="arrow-paq-{{ $rol->id }}-{{ $slugPaquete }}"
                      style="color:var(--accent); font-size:.75rem; transition:transform .2s;">▶</span>
                <span style="font-family:'Barlow Condensed',sans-serif; font-weight:700; font-size:.95rem; text-transform:uppercase; letter-spacing:.06em;">
                    {{ $paquete }}
                </span>
                <span id="cnt-paq-{{ $rol->id }}-{{ $slugPaquete }}"
                      style="font-size:.72rem; color:var(--muted);">
                    {{ $activosPaquete }}/{{ $totalPaquete }}
                </span>
            </div>

            {{-- Toggle masivo del paquete --}}
            <div style="display:flex; align-items:center; gap:.75rem;" onclick="event.stopPropagation()">
                <span style="font-size:.72rem; color:var(--muted);">Activar todo el paquete</span>
                <label style="display:flex; align-items:center; cursor:pointer;">
                    <div style="position:relative; width:42px; height:24px;">
                        <input type="checkbox" id="toggle-paq-{{ $rol->id }}-{{ $slugPaquete }}"
                               {{ $todosActivos ? 'checked' : '' }}
                               onchange="togglePaquete({{ $rol->id }}, '{{ $paquete }}', this.checked)"
                               style="opacity:0; position:absolute; width:100%; height:100%; cursor:pointer; z-index:2; margin:0;">
                        <div id="slider-paq-{{ $rol->id }}-{{ $slugPaquete }}"
                             style="position:absolute; inset:0; border-radius:12px; transition:background .2s;
                                    background:{{ $todosActivos ? 'rgba(245,166,35,.25)' : 'var(--border)' }};
                                    border:{{ $todosActivos ? '1px solid var(--accent)' : '1px solid transparent' }};">
                            <div style="position:absolute; width:18px; height:18px; border-radius:50%; top:3px;
                                        transition:transform .2s, background .2s;
                                        transform:{{ $todosActivos ? 'translateX(18px)' : 'translateX(2px)' }};
                                        background:{{ $todosActivos ? 'var(--accent)' : 'var(--muted)' }};">
                            </div>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        {{-- Contenido del paquete (colapsable) --}}
        <div id="paq-{{ $rol->id }}-{{ $slugPaquete }}" style="display:none;">

            {{-- CASOS DE USO (segundo nivel accordion) --}}
            @foreach($casosDeUso as $cu => $listaPermisos)
            @php
                $idsCU      = $listaPermisos->pluck('id')->toArray();
                $activosCU  = count(array_intersect($idsCU, $activosIds));
                $totalCU    = count($idsCU);
                $todosCUAct = $activosCU === $totalCU;
                $slugCU     = \Str::slug($cu);
            @endphp

            <div style="border-top:1px solid var(--border);">

                {{-- Header del CU --}}
                <div style="display:flex; align-items:center; justify-content:space-between;
                            padding:.75rem 1.5rem; cursor:pointer; user-select:none;
                            background:rgba(255,255,255,.02);"
                     onclick="toggleAccordion('cu-{{ $rol->id }}-{{ $slugCU }}')">

                    <div style="display:flex; align-items:center; gap:.65rem;">
                        <span id="arrow-cu-{{ $rol->id }}-{{ $slugCU }}"
                              style="color:var(--muted); font-size:.65rem; transition:transform .2s;">▶</span>
                        <span style="font-weight:600; font-size:.875rem;">{{ $cu }}</span>
                        <span id="cnt-cu-{{ $rol->id }}-{{ $slugCU }}"
                              style="font-size:.7rem; color:var(--muted);">
                            {{ $activosCU }}/{{ $totalCU }}
                        </span>
                    </div>

                    {{-- Toggle masivo del CU --}}
                    <div style="display:flex; align-items:center; gap:.6rem;" onclick="event.stopPropagation()">
                        <span style="font-size:.68rem; color:var(--muted);">Todo el CU</span>
                        <label style="display:flex; align-items:center; cursor:pointer;">
                            <div style="position:relative; width:36px; height:20px;">
                                <input type="checkbox" id="toggle-cu-{{ $rol->id }}-{{ $slugCU }}"
                                       {{ $todosCUAct ? 'checked' : '' }}
                                       onchange="toggleCU({{ $rol->id }}, '{{ $cu }}', this.checked, '{{ $slugCU }}', '{{ $slugPaquete }}')"
                                       style="opacity:0; position:absolute; width:100%; height:100%; cursor:pointer; z-index:2; margin:0;">
                                <div id="slider-cu-{{ $rol->id }}-{{ $slugCU }}"
                                     style="position:absolute; inset:0; border-radius:10px; transition:background .2s;
                                            background:{{ $todosCUAct ? 'rgba(245,166,35,.25)' : 'var(--border)' }};
                                            border:{{ $todosCUAct ? '1px solid var(--accent)' : '1px solid transparent' }};">
                                    <div style="position:absolute; width:14px; height:14px; border-radius:50%; top:3px;
                                                transition:transform .2s, background .2s;
                                                transform:{{ $todosCUAct ? 'translateX(16px)' : 'translateX(2px)' }};
                                                background:{{ $todosCUAct ? 'var(--accent)' : 'var(--muted)' }};">
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Permisos individuales (tercer nivel) --}}
                <div id="cu-{{ $rol->id }}-{{ $slugCU }}" style="display:none;">
                    @foreach($listaPermisos as $permiso)
                    @php $activo = in_array($permiso->id, $activosIds); @endphp
                    <div style="display:flex; align-items:center; justify-content:space-between;
                                padding:.65rem 2rem; border-top:1px solid rgba(42,48,69,.4);"
                         id="fila-{{ $rol->id }}-{{ $permiso->id }}"
                         data-rol="{{ $rol->id }}"
                         data-cu="{{ \Str::slug($cu) }}"
                         data-paquete="{{ $slugPaquete }}">

                        <div>
                            <span id="texto-{{ $rol->id }}-{{ $permiso->id }}"
                                  style="font-size:.85rem; font-weight:500; color:{{ $activo ? 'var(--text)' : 'var(--muted)' }};">
                                {{ $permiso->etiqueta }}
                            </span>
                            <span style="font-size:.68rem; color:var(--muted); margin-left:.4rem; font-family:monospace;">
                                {{ $permiso->nombre }}
                            </span>
                        </div>

                        <label style="display:flex; align-items:center; gap:.5rem; cursor:pointer;">
                            <span id="label-{{ $rol->id }}-{{ $permiso->id }}"
                                  style="font-size:.72rem; color:var(--muted);">
                                {{ $activo ? 'Activo' : 'Inactivo' }}
                            </span>
                            <div style="position:relative; width:36px; height:20px;">
                                <input type="checkbox"
                                       id="check-{{ $rol->id }}-{{ $permiso->id }}"
                                       {{ $activo ? 'checked' : '' }}
                                       onchange="togglePermiso({{ $rol->id }}, {{ $permiso->id }}, this.checked, '{{ \Str::slug($cu) }}', '{{ $slugPaquete }}')"
                                       style="opacity:0; position:absolute; width:100%; height:100%; cursor:pointer; z-index:2; margin:0;">
                                <div id="slider-{{ $rol->id }}-{{ $permiso->id }}"
                                     style="position:absolute; inset:0; border-radius:10px; transition:background .2s;
                                            background:{{ $activo ? 'rgba(245,166,35,.25)' : 'var(--border)' }};
                                            border:{{ $activo ? '1px solid var(--accent)' : '1px solid transparent' }};">
                                    <div id="circulo-{{ $rol->id }}-{{ $permiso->id }}"
                                         style="position:absolute; width:14px; height:14px; border-radius:50%; top:3px;
                                                transition:transform .2s, background .2s;
                                                transform:{{ $activo ? 'translateX(16px)' : 'translateX(2px)' }};
                                                background:{{ $activo ? 'var(--accent)' : 'var(--muted)' }};">
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>

            </div>
            @endforeach
        </div>
    </div>
    @endforeach

</div>
@endforeach

{{-- Toast --}}
<div id="toast" style="display:none; position:fixed; bottom:1.5rem; right:1.5rem; z-index:999;
                        background:var(--surface); border:1px solid var(--border); border-radius:var(--radius);
                        padding:.75rem 1.25rem; font-size:.875rem; box-shadow:0 8px 24px rgba(0,0,0,.4);
                        max-width:340px;"></div>

@push('scripts')
<script>
const totalGlobal = {{ $permisos->flatten()->flatten()->count() }};
const csrfToken   = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// ── Accordion ─────────────────────────────────────────────────
function toggleAccordion(id) {
    const el    = document.getElementById(id);
    const arrow = document.getElementById('arrow-' + id);
    const open  = el.style.display !== 'none';
    el.style.display = open ? 'none' : 'block';
    if (arrow) arrow.style.transform = open ? '' : 'rotate(90deg)';
}

// ── Tabs ──────────────────────────────────────────────────────
function mostrarRol(idRol) {
    document.querySelectorAll('[id^="panel-"]').forEach(p => p.style.display = 'none');
    document.querySelectorAll('[id^="tab-"]').forEach(t => {
        t.className = t.className.replace('btn-primary', 'btn-ghost');
    });
    document.getElementById('panel-' + idRol).style.display = '';
    document.getElementById('tab-' + idRol).className =
        document.getElementById('tab-' + idRol).className.replace('btn-ghost', 'btn-primary');
}

// ── Toggle individual ─────────────────────────────────────────
async function togglePermiso(idRol, idPermiso, activo, slugCU, slugPaquete) {
    const estado = activo ? 'Activo' : 'Inactivo';
    try {
        const res  = await fetch('{{ route("permisos.toggle") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ id_rol: idRol, id_permiso: idPermiso, estado }),
        });
        const data = await res.json();
        if (data.success) {
            actualizarVisualIndividual(idRol, idPermiso, activo);
            recalcularCU(idRol, slugCU);
            recalcularPaquete(idRol, slugPaquete);
            recalcularGlobal(idRol);
            mostrarToast(data.mensaje, 'success');
        } else {
            document.getElementById('check-' + idRol + '-' + idPermiso).checked = !activo;
            mostrarToast('Error al actualizar.', 'error');
        }
    } catch(e) {
        document.getElementById('check-' + idRol + '-' + idPermiso).checked = !activo;
        mostrarToast('Error de conexión.', 'error');
    }
}

// ── Toggle CU completo ────────────────────────────────────────
async function toggleCU(idRol, cu, activo, slugCU, slugPaquete) {
    const estado = activo ? 'Activo' : 'Inactivo';
    try {
        const res  = await fetch('{{ route("permisos.toggleCU") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ id_rol: idRol, caso_uso: cu, estado }),
        });
        const data = await res.json();
        if (data.success) {
            data.ids.forEach(id => actualizarVisualIndividual(idRol, id, activo));
            recalcularCU(idRol, slugCU);
            recalcularPaquete(idRol, slugPaquete);
            recalcularGlobal(idRol);
            actualizarSliderCU(idRol, slugCU, activo);
            mostrarToast(data.mensaje, 'success');
        }
    } catch(e) { mostrarToast('Error de conexión.', 'error'); }
}

// ── Toggle Paquete completo ───────────────────────────────────
async function togglePaquete(idRol, paquete, activo) {
    const slugPaq = slugify(paquete);
    const estado  = activo ? 'Activo' : 'Inactivo';
    try {
        const res  = await fetch('{{ route("permisos.togglePaquete") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ id_rol: idRol, paquete: paquete, estado }),
        });
        const data = await res.json();
        if (data.success) {
            data.ids.forEach(id => actualizarVisualIndividual(idRol, id, activo));
            // Recalcular todos los CU del paquete
            document.querySelectorAll(`[data-rol="${idRol}"][data-paquete="${slugPaq}"]`).forEach(fila => {
                const slugCU = fila.dataset.cu;
                recalcularCU(idRol, slugCU);
                actualizarSliderCU(idRol, slugCU, activo);
            });
            recalcularPaquete(idRol, slugPaq);
            recalcularGlobal(idRol);
            actualizarSliderPaquete(idRol, slugPaq, activo);
            mostrarToast(data.mensaje, 'success');
        }
    } catch(e) { mostrarToast('Error de conexión.', 'error'); }
}

// ── Helpers visuales ──────────────────────────────────────────
function actualizarVisualIndividual(idRol, idPermiso, activo) {
    const slider  = document.getElementById('slider-'  + idRol + '-' + idPermiso);
    const circulo = document.getElementById('circulo-' + idRol + '-' + idPermiso);
    const label   = document.getElementById('label-'   + idRol + '-' + idPermiso);
    const texto   = document.getElementById('texto-'   + idRol + '-' + idPermiso);
    const check   = document.getElementById('check-'   + idRol + '-' + idPermiso);
    if (!slider) return;
    if (activo) {
        slider.style.background  = 'rgba(245,166,35,.25)';
        slider.style.border      = '1px solid var(--accent)';
        circulo.style.transform  = 'translateX(16px)';
        circulo.style.background = 'var(--accent)';
        label.textContent        = 'Activo';
        texto.style.color        = 'var(--text)';
        if (check) check.checked = true;
    } else {
        slider.style.background  = 'var(--border)';
        slider.style.border      = '1px solid transparent';
        circulo.style.transform  = 'translateX(2px)';
        circulo.style.background = 'var(--muted)';
        label.textContent        = 'Inactivo';
        texto.style.color        = 'var(--muted)';
        if (check) check.checked = false;
    }
}

function actualizarSliderCU(idRol, slugCU, activo) {
    const s = document.getElementById('slider-cu-' + idRol + '-' + slugCU);
    const c = s?.querySelector('div');
    const t = document.getElementById('toggle-cu-' + idRol + '-' + slugCU);
    if (!s) return;
    if (activo) { s.style.background='rgba(245,166,35,.25)'; s.style.border='1px solid var(--accent)'; c.style.transform='translateX(16px)'; c.style.background='var(--accent)'; if(t) t.checked=true; }
    else { s.style.background='var(--border)'; s.style.border='1px solid transparent'; c.style.transform='translateX(2px)'; c.style.background='var(--muted)'; if(t) t.checked=false; }
}

function actualizarSliderPaquete(idRol, slugPaq, activo) {
    const s = document.getElementById('slider-paq-' + idRol + '-' + slugPaq);
    const c = s?.querySelector('div');
    const t = document.getElementById('toggle-paq-' + idRol + '-' + slugPaq);
    if (!s) return;
    if (activo) { s.style.background='rgba(245,166,35,.25)'; s.style.border='1px solid var(--accent)'; c.style.transform='translateX(18px)'; c.style.background='var(--accent)'; if(t) t.checked=true; }
    else { s.style.background='var(--border)'; s.style.border='1px solid transparent'; c.style.transform='translateX(2px)'; c.style.background='var(--muted)'; if(t) t.checked=false; }
}

function recalcularCU(idRol, slugCU) {
    const filas = document.querySelectorAll(`[id^="fila-${idRol}-"][data-cu="${slugCU}"]`);
    let a=0, t=0;
    filas.forEach(f => { const id=f.id.split('-')[2]; const c=document.getElementById(`check-${idRol}-${id}`); if(c?.checked) a++; t++; });
    const cnt = document.getElementById(`cnt-cu-${idRol}-${slugCU}`);
    if (cnt) cnt.textContent = `${a}/${t}`;
}

function recalcularPaquete(idRol, slugPaq) {
    const filas = document.querySelectorAll(`[id^="fila-${idRol}-"][data-paquete="${slugPaq}"]`);
    let a=0, t=0;
    filas.forEach(f => { const id=f.id.split('-')[2]; const c=document.getElementById(`check-${idRol}-${id}`); if(c?.checked) a++; t++; });
    const cnt = document.getElementById(`cnt-paq-${idRol}-${slugPaq}`);
    if (cnt) cnt.textContent = `${a}/${t}`;
}

function recalcularGlobal(idRol) {
    const all = document.querySelectorAll(`#panel-${idRol} input[type="checkbox"][id^="check-"]`);
    let a=0;
    all.forEach(c => { if(c.checked) a++; });
    const cnt = document.getElementById(`contador-global-${idRol}`);
    if (cnt) cnt.textContent = `${a} privilegio(s) activo(s) de ${totalGlobal}`;
}

function slugify(str) {
    return str.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
}

// ── Toast ──────────────────────────────────────────────────────
function mostrarToast(msg, tipo) {
    const t = document.getElementById('toast');
    t.textContent = (tipo==='success'?'✓ ':'⚠ ') + msg;
    t.style.borderColor = tipo==='success'?'rgba(46,204,113,.3)':'rgba(231,76,60,.3)';
    t.style.color       = tipo==='success'?'var(--success)':'var(--danger)';
    t.style.display='block'; t.style.opacity='1';
    clearTimeout(window._toast);
    window._toast = setTimeout(()=>{ t.style.opacity='0'; setTimeout(()=>t.style.display='none',300); }, 2800);
}
</script>
@endpush

@endsection