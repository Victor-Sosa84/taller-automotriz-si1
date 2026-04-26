@extends('layouts.app')
@section('title', 'Cargos — ' . $usuario->persona->nombre)

@section('content')

<div style="margin-bottom:1.5rem;">
    <a href="{{ route('usuarios.index') }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">
        ← Volver a usuarios
    </a>
</div>

{{-- Encabezado --}}
<div style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:1rem; margin-bottom:1.75rem;">
    <div>
        <div style="font-size:.75rem; color:var(--muted); letter-spacing:.08em; text-transform:uppercase; margin-bottom:.3rem;">
            Gestión de cargos laborales
        </div>
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:2rem; font-weight:800; text-transform:uppercase; line-height:1;">
            {{ $usuario->persona->nombre }}
        </h2>
        <div style="display:flex; align-items:center; gap:.75rem; margin-top:.4rem; flex-wrap:wrap;">
            <span class="badge {{ match($usuario->id_rol) { 1 => 'badge-admin', 2 => 'badge-mec', 3 => 'badge-recep', default => '' } }}">
                {{ $usuario->rol?->nombre }}
            </span>
            <span style="font-size:.8rem; color:var(--muted); font-family:monospace;">
                {{ $usuario->nombre_usuario }}
            </span>
        </div>
    </div>
    <a href="{{ route('usuarios.edit', $usuario->id_usuario) }}" class="btn btn-ghost">
        ✏ Editar usuario
    </a>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; align-items:start;">

    {{-- ── PANEL IZQUIERDO: Cargos actuales ── --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">💼 Cargos asignados</span>
            <span style="font-size:.75rem; color:var(--muted);">
                {{ count($tiposAsignados) }} cargo(s)
            </span>
        </div>

        @if($usuario->persona->tiposTrabajador->isNotEmpty())
            <div style="padding:.5rem 0;">
                @foreach($usuario->persona->tiposTrabajador as $tipo)
                <div style="display:flex; align-items:center; justify-content:space-between;
                            padding:.8rem 1.25rem; border-bottom:1px solid var(--border);">
                    <div style="display:flex; align-items:center; gap:.6rem;">
                        <span style="color:var(--accent); font-size:1rem;">⚙</span>
                        <span style="font-weight:500; font-size:.9rem;">{{ $tipo->descripcion }}</span>
                    </div>
                    <form action="{{ route('cargos.destroy', [$usuario->id_usuario, $tipo->id]) }}"
                          method="POST"
                          onsubmit="return confirm('¿Quitar el cargo «{{ $tipo->descripcion }}» de este usuario?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" title="Quitar cargo">
                            ✕ Quitar
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        @else
            <div style="padding:2rem; text-align:center; color:var(--muted); font-size:.875rem;">
                <div style="font-size:1.75rem; opacity:.3; margin-bottom:.5rem;">💼</div>
                Este usuario no tiene cargos asignados aún.
            </div>
        @endif
    </div>

    {{-- ── PANEL DERECHO: Asignar nuevo cargo ── --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">＋ Asignar cargo</span>
        </div>
        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success" style="margin-bottom:1rem;">
                    ✓ {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-error" style="margin-bottom:1rem;">
                    ⚠ {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('cargos.store', $usuario->id_usuario) }}" method="POST">
                @csrf
                <div class="field-group" style="margin-bottom:1rem;">
                    <label>Tipo de cargo</label>
                    <select name="id_tipo_trabajador" required>
                        <option value="" disabled selected>Seleccionar cargo...</option>
                        @foreach($tiposTodos as $tipo)
                            <option value="{{ $tipo->id }}"
                                {{ in_array($tipo->id, $tiposAsignados) ? 'disabled' : '' }}>
                                {{ $tipo->descripcion }}
                                {{ in_array($tipo->id, $tiposAsignados) ? '(ya asignado)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;">
                    ＋ Asignar cargo
                </button>
            </form>

            <div style="margin-top:1.5rem; padding-top:1rem; border-top:1px solid var(--border);">
                <div style="font-size:.72rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--muted); margin-bottom:.6rem;">
                    Todos los tipos disponibles
                </div>
                @foreach($tiposTodos as $tipo)
                <div style="display:flex; align-items:center; justify-content:space-between;
                            padding:.4rem 0; font-size:.82rem; border-bottom:1px solid rgba(42,48,69,.4);">
                    <span style="color:{{ in_array($tipo->id, $tiposAsignados) ? 'var(--accent)' : 'var(--muted)' }};">
                        {{ $tipo->descripcion }}
                    </span>
                    @if(in_array($tipo->id, $tiposAsignados))
                        <span style="font-size:.7rem; color:var(--accent);">✓ asignado</span>
                    @endif
                </div>
                @endforeach
            </div>

        </div>
    </div>

</div>

@endsection
