@extends('layouts.app')
@section('title', 'Gestión de Permisos')

@section('content')

<div style="margin-bottom:1.5rem;">
    <a href="{{ route('usuarios.index') }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">
        ← Volver a usuarios
    </a>
    <div style="margin-top:.5rem;">
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; text-transform:uppercase;">
            Gestión de Permisos
        </h2>
        <p style="color:var(--muted); font-size:.85rem; margin-top:.2rem;">
            Activa o desactiva permisos por rol. Los cambios se aplican de inmediato.
        </p>
    </div>
</div>

{{-- Tabs de roles --}}
<div style="display:flex; gap:.5rem; margin-bottom:1.5rem; flex-wrap:wrap;">
    @foreach($roles as $rol)
    <button onclick="mostrarRol({{ $rol->id }})"
            id="tab-{{ $rol->id }}"
            class="btn {{ $loop->first ? 'btn-primary' : 'btn-ghost' }}"
            style="font-family:'Barlow Condensed',sans-serif; font-weight:700; letter-spacing:.05em;">
        @php $icono = match($rol->id) { 1 => '⚙', 2 => '🔧', 3 => '📋', default => '👤' }; @endphp
        {{ $icono }} {{ $rol->nombre }}
    </button>
    @endforeach
</div>

{{-- Panel por rol --}}
@foreach($roles as $rol)
@php
    $activosIds = $rol->permisos
        ->filter(fn($p) => $p->pivot->estado === 'Activo')
        ->pluck('id')
        ->toArray();
    $totalPermisos = $permisos->flatten()->count();
@endphp

<div id="panel-{{ $rol->id }}" style="{{ $loop->first ? '' : 'display:none;' }}">

    {{-- Header del rol --}}
    <div style="background:var(--surface2); border:1px solid var(--border); border-radius:var(--radius);
                padding:1rem 1.25rem; margin-bottom:1.25rem; display:flex; align-items:center; justify-content:space-between;">
        <div>
            <span style="font-family:'Barlow Condensed',sans-serif; font-size:1.1rem; font-weight:800; text-transform:uppercase;">
                {{ $rol->nombre }}
            </span>
            <span id="contador-global-{{ $rol->id }}" style="color:var(--muted); font-size:.8rem; margin-left:.75rem;">
                {{ count($activosIds) }} permiso(s) activo(s) de {{ $totalPermisos }}
            </span>
        </div>
        <span class="badge {{ match($rol->id) { 1 => 'badge-admin', 2 => 'badge-mec', 3 => 'badge-recep', default => '' } }}">
            {{ $rol->descripcion }}
        </span>
    </div>

    {{-- Módulos con permisos --}}
    @foreach($permisos as $modulo => $lista)
    @php $activosEnModulo = $lista->filter(fn($p) => in_array($p->id, $activosIds))->count(); @endphp
    <div class="card" style="margin-bottom:1rem;">
        <div class="card-header">
            <span class="card-title" style="font-size:.8rem;">{{ $modulo }}</span>
            <span id="contador-{{ $rol->id }}-{{ \Str::slug($modulo) }}"
                  style="font-size:.72rem; color:var(--muted);">
                {{ $activosEnModulo }}/{{ $lista->count() }} activos
            </span>
        </div>
        <div style="padding:.25rem 0;">
            @foreach($lista as $permiso)
            @php $activo = in_array($permiso->id, $activosIds); @endphp
            <div style="display:flex; align-items:center; justify-content:space-between;
                        padding:.7rem 1.25rem; border-bottom:1px solid var(--border);"
                 id="fila-{{ $rol->id }}-{{ $permiso->id }}"
                 data-rol="{{ $rol->id }}"
                 data-modulo="{{ \Str::slug($modulo) }}"
                 data-total-modulo="{{ $lista->count() }}">

                <div>
                    <span id="texto-{{ $rol->id }}-{{ $permiso->id }}"
                          style="font-size:.875rem; font-weight:500; color:{{ $activo ? 'var(--text)' : 'var(--muted)' }};">
                        {{ $permiso->etiqueta }}
                    </span>
                    <span style="font-size:.72rem; color:var(--muted); margin-left:.5rem; font-family:monospace;">
                        {{ $permiso->nombre }}
                    </span>
                </div>

                {{-- Toggle --}}
                <label style="display:flex; align-items:center; gap:.6rem; cursor:pointer;">
                    <span id="label-{{ $rol->id }}-{{ $permiso->id }}"
                          style="font-size:.75rem; color:var(--muted);">
                        {{ $activo ? 'Activo' : 'Inactivo' }}
                    </span>
                    <div style="position:relative; width:42px; height:24px;">
                        <input type="checkbox"
                               id="check-{{ $rol->id }}-{{ $permiso->id }}"
                               {{ $activo ? 'checked' : '' }}
                               onchange="togglePermiso({{ $rol->id }}, {{ $permiso->id }}, this.checked, '{{ \Str::slug($modulo) }}')"
                               style="opacity:0; position:absolute; width:100%; height:100%; cursor:pointer; z-index:2; margin:0;">
                        <div id="slider-{{ $rol->id }}-{{ $permiso->id }}"
                             style="position:absolute; inset:0; border-radius:12px; transition:background .2s;
                                    background:{{ $activo ? 'rgba(245,166,35,.25)' : 'var(--border)' }};
                                    border:{{ $activo ? '1px solid var(--accent)' : '1px solid transparent' }};">
                            <div id="circulo-{{ $rol->id }}-{{ $permiso->id }}"
                                 style="position:absolute; width:18px; height:18px; border-radius:50%; top:3px;
                                        transition:transform .2s, background .2s;
                                        transform:{{ $activo ? 'translateX(18px)' : 'translateX(2px)' }};
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
@endforeach

{{-- Toast --}}
<div id="toast" style="display:none; position:fixed; bottom:1.5rem; right:1.5rem; z-index:999;
                        background:var(--surface); border:1px solid var(--border); border-radius:var(--radius);
                        padding:.75rem 1.25rem; font-size:.875rem; box-shadow:0 8px 24px rgba(0,0,0,.4);
                        max-width:320px;">
</div>

@push('scripts')
<script>
// ── Totales por rol (calculados en PHP, pasados a JS) ─────────
const totalGlobal = {{ $permisos->flatten()->count() }};

// ── Tab switching ─────────────────────────────────────────────
function mostrarRol(idRol) {
    document.querySelectorAll('[id^="panel-"]').forEach(p => p.style.display = 'none');
    document.querySelectorAll('[id^="tab-"]').forEach(t => {
        t.className = t.className.replace('btn-primary', 'btn-ghost');
    });
    document.getElementById('panel-' + idRol).style.display = '';
    document.getElementById('tab-' + idRol).className =
        document.getElementById('tab-' + idRol).className.replace('btn-ghost', 'btn-primary');
}

// ── Toggle permiso ────────────────────────────────────────────
async function togglePermiso(idRol, idPermiso, activo, slugModulo) {
    const estado = activo ? 'Activo' : 'Inactivo';

    try {
        const res = await fetch('{{ route("permisos.toggle") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({ id_rol: idRol, id_permiso: idPermiso, estado }),
        });

        const data = await res.json();

        if (data.success) {
            actualizarVisual(idRol, idPermiso, activo, slugModulo);
            mostrarToast(data.mensaje, 'success');
        } else {
            // Revertir el checkbox si falló
            document.getElementById('check-' + idRol + '-' + idPermiso).checked = !activo;
            mostrarToast('Error al actualizar.', 'error');
        }
    } catch (e) {
        document.getElementById('check-' + idRol + '-' + idPermiso).checked = !activo;
        mostrarToast('Error de conexión.', 'error');
    }
}

// ── Actualizar visual del toggle, texto y contadores ─────────
function actualizarVisual(idRol, idPermiso, activo, slugModulo) {
    const slider  = document.getElementById('slider-'  + idRol + '-' + idPermiso);
    const circulo = document.getElementById('circulo-' + idRol + '-' + idPermiso);
    const label   = document.getElementById('label-'   + idRol + '-' + idPermiso);
    const texto   = document.getElementById('texto-'   + idRol + '-' + idPermiso);

    if (activo) {
        slider.style.background  = 'rgba(245,166,35,.25)';
        slider.style.border      = '1px solid var(--accent)';
        circulo.style.transform  = 'translateX(18px)';
        circulo.style.background = 'var(--accent)';
        label.textContent        = 'Activo';
        texto.style.color        = 'var(--text)';
    } else {
        slider.style.background  = 'var(--border)';
        slider.style.border      = '1px solid transparent';
        circulo.style.transform  = 'translateX(2px)';
        circulo.style.background = 'var(--muted)';
        label.textContent        = 'Inactivo';
        texto.style.color        = 'var(--muted)';
    }

    // Recalcular contador del módulo
    recalcularContador(idRol, slugModulo);

    // Recalcular contador global
    recalcularContadorGlobal(idRol);
}

// ── Recalcular contador por módulo ───────────────────────────
function recalcularContador(idRol, slugModulo) {
    // Contar checkboxes activos en filas de ese módulo y rol
    const filas = document.querySelectorAll(
        '[id^="fila-' + idRol + '-"][data-modulo="' + slugModulo + '"]'
    );
    let activos = 0;
    let total   = 0;
    filas.forEach(fila => {
        const idPermiso = fila.id.split('-')[2];
        const check = document.getElementById('check-' + idRol + '-' + idPermiso);
        if (check && check.checked) activos++;
        total++;
    });
    const contador = document.getElementById('contador-' + idRol + '-' + slugModulo);
    if (contador) contador.textContent = activos + '/' + total + ' activos';
}

// ── Recalcular contador global del rol ───────────────────────
function recalcularContadorGlobal(idRol) {
    const checks = document.querySelectorAll(
        '#panel-' + idRol + ' input[type="checkbox"]'
    );
    let activos = 0;
    checks.forEach(c => { if (c.checked) activos++; });
    const contador = document.getElementById('contador-global-' + idRol);
    if (contador) contador.textContent = activos + ' permiso(s) activo(s) de ' + totalGlobal;
}

// ── Toast ─────────────────────────────────────────────────────
function mostrarToast(mensaje, tipo) {
    const toast = document.getElementById('toast');
    toast.textContent  = (tipo === 'success' ? '✓ ' : '⚠ ') + mensaje;
    toast.style.borderColor = tipo === 'success' ? 'rgba(46,204,113,.3)' : 'rgba(231,76,60,.3)';
    toast.style.color       = tipo === 'success' ? 'var(--success)' : 'var(--danger)';
    toast.style.display     = 'block';
    toast.style.opacity     = '1';
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.style.display = 'none', 300);
    }, 2500);
}
</script>
@endpush

@endsection