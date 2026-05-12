@extends('layouts.app')
@section('title', 'Gestión de Privilegios')

@section('content')

<div style="margin-bottom:1.5rem;">
    <a href="{{ route('roles.index') }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">← Volver a roles</a>
    <div style="margin-top:.5rem;">
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; text-transform:uppercase;">Gestión de Privilegios</h2>
        <p style="color:var(--muted); font-size:.85rem; margin-top:.2rem;">Estructura: Paquete → Caso de Uso → Privilegio granular.</p>
    </div>
</div>

{{-- Tabs de roles --}}
<div style="display:flex; gap:.5rem; margin-bottom:1.25rem; flex-wrap:wrap; align-items:center;">
    <span style="font-size:.75rem; color:var(--muted); font-weight:700; text-transform:uppercase; letter-spacing:.06em; margin-right:.25rem;">Perfil:</span>
    @foreach($roles as $rol)
    <button onclick="seleccionarRol({{ $rol->id }}, '{{ addslashes($rol->nombre) }}', {{ $rol->id === 1 ? 'true' : 'false' }})"
            id="tab-{{ $rol->id }}"
            class="btn {{ $loop->first ? 'btn-primary' : 'btn-ghost' }}"
            style="font-family:'Barlow Condensed',sans-serif; font-weight:700;">
        {{ $rol->nombre }}
        @if($rol->id === 1) <span style="font-size:.65rem; opacity:.6;">🔒</span> @endif
    </button>
    @endforeach
</div>

{{-- Header rol activo --}}
<div style="background:var(--surface2); border:1px solid var(--border); border-radius:var(--radius); padding:.9rem 1.25rem; margin-bottom:1.25rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem;">
    <div style="display:flex; align-items:center; gap:.75rem;">
        <span id="rol-nombre" style="font-family:'Barlow Condensed',sans-serif; font-size:1.1rem; font-weight:800; text-transform:uppercase;">{{ $roles->first()->nombre }}</span>
        <span id="aviso-bloqueado" style="display:{{ $roles->first()->id === 1 ? 'inline' : 'none' }}; font-size:.72rem; color:var(--danger); background:rgba(231,76,60,.1); border:1px solid rgba(231,76,60,.25); border-radius:3px; padding:.15rem .5rem;">
            Rol base — no modificable
        </span>
    </div>
    <span id="contador-global" style="font-size:.8rem; color:var(--muted);">cargando...</span>
</div>

{{-- Acordeón --}}
@foreach($permisos as $paquete => $cuGrupos)
@php $pSlug = 'p-'.preg_replace('/[^a-z0-9]/', '-', strtolower($paquete)); @endphp

<div class="card" style="margin-bottom:.75rem;">

    <div id="hdr-{{ $pSlug }}" onclick="toggleBloque('{{ $pSlug }}')"
         style="padding:1rem 1.25rem; display:flex; align-items:center; justify-content:space-between; cursor:pointer; border-radius:var(--radius);">
        <div style="display:flex; align-items:center; gap:.75rem;">
            <span id="arr-{{ $pSlug }}" style="font-size:.75rem; color:var(--accent); transition:transform .2s; display:inline-block;">▶</span>
            <span style="font-family:'Barlow Condensed',sans-serif; font-size:1rem; font-weight:800; text-transform:uppercase; letter-spacing:.04em;">{{ $paquete }}</span>
            <span id="cnt-{{ $pSlug }}" style="font-size:.72rem; color:var(--muted);">0/0</span>
        </div>
        <div id="btns-paquete-{{ $pSlug }}" style="display:flex; gap:.4rem;" onclick="event.stopPropagation()">
            <button onclick="toggleMasivoPaquete('{{ $paquete }}', 'Activo')" class="btn btn-sm btn-ghost" style="font-size:.7rem; color:var(--success); padding:.3rem .6rem;">✓ Activar paquete</button>
            <button onclick="toggleMasivoPaquete('{{ $paquete }}', 'Inactivo')" class="btn btn-sm btn-ghost" style="font-size:.7rem; color:var(--danger); padding:.3rem .6rem;">✕ Desactivar</button>
        </div>
    </div>

    <div id="body-{{ $pSlug }}" style="display:none; border-top:1px solid var(--border);">
        @foreach($cuGrupos as $cu => $listaPermisos)
        @php $cSlug = 'c-'.preg_replace('/[^a-z0-9]/', '-', strtolower($cu)); @endphp
        <div style="border-bottom:1px solid rgba(42,48,69,.5);">
            <div onclick="toggleBloque('{{ $cSlug }}')"
                 style="padding:.7rem 1.25rem .7rem 2rem; display:flex; align-items:center; justify-content:space-between; cursor:pointer; background:var(--surface2);">
                <div style="display:flex; align-items:center; gap:.6rem;">
                    <span id="arr-{{ $cSlug }}" style="font-size:.65rem; color:var(--muted); transition:transform .2s; display:inline-block;">▶</span>
                    <span style="font-size:.82rem; font-weight:700;">{{ $cu }}</span>
                    <span id="cnt-{{ $cSlug }}" style="font-size:.68rem; color:var(--muted);">0/0</span>
                </div>
                <div id="btns-cu-{{ $cSlug }}" style="display:flex; gap:.3rem;" onclick="event.stopPropagation()">
                    <button onclick="toggleMasivoCU('{{ $cu }}', 'Activo')" class="btn btn-sm btn-ghost" style="font-size:.66rem; color:var(--success); padding:.2rem .45rem;">✓</button>
                    <button onclick="toggleMasivoCU('{{ $cu }}', 'Inactivo')" class="btn btn-sm btn-ghost" style="font-size:.66rem; color:var(--danger); padding:.2rem .45rem;">✕</button>
                </div>
            </div>
            <div id="body-{{ $cSlug }}" style="display:none;">
                @foreach($listaPermisos as $permiso)
                <div style="display:flex; align-items:center; justify-content:space-between; padding:.55rem 1.25rem .55rem 3rem; border-top:1px solid rgba(42,48,69,.4);"
                     data-pid="{{ $permiso->id }}" data-cu="{{ $cu }}" data-paquete="{{ $paquete }}" data-pslug="{{ $pSlug }}" data-cslug="{{ $cSlug }}">
                    <div>
                        <span id="txt-{{ $permiso->id }}" style="font-size:.845rem; font-weight:500; color:var(--muted);">{{ $permiso->etiqueta }}</span>
                        <span style="font-size:.68rem; color:var(--muted); margin-left:.35rem; font-family:monospace;">{{ $permiso->nombre }}</span>
                    </div>
                    <label style="display:flex; align-items:center; gap:.5rem; cursor:pointer;">
                        <span id="lbl-{{ $permiso->id }}" style="font-size:.7rem; color:var(--muted);">—</span>
                        <div style="position:relative; width:38px; height:22px;">
                            <input type="checkbox" id="chk-{{ $permiso->id }}"
                                   onchange="togglePermiso({{ $permiso->id }}, this.checked)"
                                   style="opacity:0; position:absolute; width:100%; height:100%; cursor:pointer; z-index:2; margin:0;">
                            <div id="sldr-{{ $permiso->id }}" style="position:absolute; inset:0; border-radius:11px; background:var(--border); border:1px solid transparent; transition:background .2s;">
                                <div id="circ-{{ $permiso->id }}" style="position:absolute; width:16px; height:16px; border-radius:50%; top:2px; transform:translateX(2px); background:var(--muted); transition:transform .2s, background .2s;"></div>
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

<div id="toast" style="display:none; position:fixed; bottom:1.5rem; right:1.5rem; z-index:999; background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:.75rem 1.25rem; font-size:.875rem; box-shadow:0 8px 24px rgba(0,0,0,.4); max-width:340px;"></div>

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
let rolActual = {{ $roles->first()->id }};
let rolBloqueado = {{ $roles->first()->id === 1 ? 'true' : 'false' }};
let activos = {};

document.addEventListener('DOMContentLoaded', () => cargarPermisos(rolActual));

function seleccionarRol(id, nombre, bloqueado) {
    rolActual = id;
    rolBloqueado = bloqueado;
    document.querySelectorAll('[id^="tab-"]').forEach(t => t.className = t.className.replace('btn-primary','btn-ghost'));
    document.getElementById('tab-'+id).className = document.getElementById('tab-'+id).className.replace('btn-ghost','btn-primary');
    document.getElementById('rol-nombre').textContent = nombre;
    document.getElementById('aviso-bloqueado').style.display = bloqueado ? 'inline' : 'none';
    // Mostrar/ocultar todos los botones de acción
    document.querySelectorAll('[id^="btns-"]').forEach(b => b.style.display = bloqueado ? 'none' : 'flex');
    // Deshabilitar/habilitar checkboxes
    document.querySelectorAll('[id^="chk-"]').forEach(c => c.disabled = bloqueado);
    cargarPermisos(id);
}

async function cargarPermisos(id) {
    document.getElementById('contador-global').textContent = 'cargando...';
    try {
        const r = await fetch(`/api/rol/${id}/permisos`, { headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF} });
        const ids = await r.json();
        activos = {};
        ids.forEach(i => activos[i] = true);
        renderizar();
    } catch(e) { toast('Error al cargar privilegios.','error'); }
}

function renderizar() {
    document.querySelectorAll('[data-pid]').forEach(fila => {
        const id = parseInt(fila.dataset.pid);
        visual(id, !!activos[id]);
    });
    contadores();
}

async function togglePermiso(id, on) {
    if (rolBloqueado) { document.getElementById('chk-'+id).checked = !on; toast('El rol base no puede modificarse.','error'); return; }
    const estado = on ? 'Activo' : 'Inactivo';
    try {
        const r = await fetch('{{ route("permisos.toggle") }}', {
            method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
            body: JSON.stringify({id_rol:rolActual, id_permiso:id, estado})
        });
        const d = await r.json();
        if (d.success) { activos[id]=on; visual(id,on); contadores(); toast(d.mensaje,'success'); }
        else { document.getElementById('chk-'+id).checked=!on; toast(d.mensaje,'error'); }
    } catch(e) { document.getElementById('chk-'+id).checked=!on; toast('Error de conexión.','error'); }
}

async function toggleMasivoCU(cu, estado) {
    if (rolBloqueado) return;
    try {
        const r = await fetch('{{ route("permisos.toggleCU") }}', {
            method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
            body: JSON.stringify({id_rol:rolActual, caso_uso:cu, estado})
        });
        const d = await r.json();
        if (d.success) {
            const on = estado==='Activo';
            d.ids.forEach(id => { activos[id]=on; visual(id,on); });
            contadores(); toast(d.mensaje,'success');
        }
    } catch(e) { toast('Error de conexión.','error'); }
}

async function toggleMasivoPaquete(paquete, estado) {
    if (rolBloqueado) return;
    try {
        const r = await fetch('{{ route("permisos.togglePaquete") }}', {
            method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
            body: JSON.stringify({id_rol:rolActual, paquete:paquete, estado})
        });
        const d = await r.json();
        if (d.success) {
            const on = estado==='Activo';
            d.ids.forEach(id => { activos[id]=on; visual(id,on); });
            contadores(); toast(d.mensaje,'success');
        }
    } catch(e) { toast('Error de conexión.','error'); }
}

function visual(id, on) {
    const sldr=document.getElementById('sldr-'+id);
    const circ=document.getElementById('circ-'+id);
    const lbl=document.getElementById('lbl-'+id);
    const txt=document.getElementById('txt-'+id);
    const chk=document.getElementById('chk-'+id);
    if (!sldr) return;
    if (on) {
        sldr.style.background='rgba(245,166,35,.25)'; sldr.style.border='1px solid var(--accent)';
        circ.style.transform='translateX(18px)'; circ.style.background='var(--accent)';
        lbl.textContent='Activo'; txt.style.color='var(--text)'; if(chk) chk.checked=true;
    } else {
        sldr.style.background='var(--border)'; sldr.style.border='1px solid transparent';
        circ.style.transform='translateX(2px)'; circ.style.background='var(--muted)';
        lbl.textContent='Inactivo'; txt.style.color='var(--muted)'; if(chk) chk.checked=false;
    }
}

function contadores() {
    const pMap={}, cMap={};
    document.querySelectorAll('[data-pid]').forEach(f => {
        const id=parseInt(f.dataset.pid);
        const pSlug=f.dataset.pslug, cSlug=f.dataset.cslug;
        if(!pMap[pSlug]) pMap[pSlug]={a:0,t:0};
        if(!cMap[cSlug]) cMap[cSlug]={a:0,t:0};
        pMap[pSlug].t++; cMap[cSlug].t++;
        if(activos[id]) { pMap[pSlug].a++; cMap[cSlug].a++; }
    });
    Object.entries(pMap).forEach(([s,v]) => { const e=document.getElementById('cnt-'+s); if(e) e.textContent=`${v.a}/${v.t}`; });
    Object.entries(cMap).forEach(([s,v]) => { const e=document.getElementById('cnt-'+s); if(e) e.textContent=`${v.a}/${v.t}`; });
    const tot=document.querySelectorAll('[data-pid]').length;
    const act=Object.values(activos).filter(Boolean).length;
    document.getElementById('contador-global').textContent=`${act} privilegio(s) activo(s) de ${tot}`;
}

function toggleBloque(slug) {
    const body=document.getElementById('body-'+slug);
    const arr=document.getElementById('arr-'+slug);
    const open=body.style.display==='none';
    body.style.display=open?'':'none';
    arr.style.transform=open?'rotate(90deg)':'';
}

function toast(msg, tipo) {
    const t=document.getElementById('toast');
    t.textContent=(tipo==='success'?'✓ ':'⚠ ')+msg;
    t.style.borderColor=tipo==='success'?'rgba(46,204,113,.3)':'rgba(231,76,60,.3)';
    t.style.color=tipo==='success'?'var(--success)':'var(--danger)';
    t.style.display='block'; t.style.opacity='1';
    clearTimeout(window._t);
    window._t=setTimeout(()=>{t.style.opacity='0';setTimeout(()=>t.style.display='none',300);},2500);
}

// Aplicar estado bloqueado inicial si el primer rol es el base
if (rolBloqueado) {
    document.querySelectorAll('[id^="btns-"]').forEach(b => b.style.display='none');
    document.querySelectorAll('[id^="chk-"]').forEach(c => c.disabled=true);
}
</script>
@endpush
@endsection