@extends('layouts.app')
@section('title', 'Roles / Perfiles')

@section('content')

<div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
    <div>
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; text-transform:uppercase;">
            Roles / Perfiles
        </h2>
        <p style="color:var(--muted); font-size:.85rem; margin-top:.2rem;">
            Gestiona los perfiles de acceso del sistema. Los privilegios se asignan desde la sección Permisos.
        </p>
    </div>
    <a href="{{ route('roles.create') }}" class="btn btn-primary">＋ Nuevo perfil</a>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre del perfil</th>
                <th>Descripción</th>
                <th>Usuarios asignados</th>
                <th style="text-align:center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($roles as $rol)
            <tr>
                <td class="td-muted">#{{ $rol->id }}</td>
                <td style="font-weight:600;">
                    {{ $rol->nombre }}
                    @if($rol->id === 1)
                        <span class="badge badge-admin" style="margin-left:.4rem;">Base</span>
                    @endif
                </td>
                <td class="td-muted">{{ $rol->descripcion ?? '—' }}</td>
                <td>
                    <span style="font-family:'Barlow Condensed',sans-serif; font-size:1.1rem; font-weight:700; color:var(--accent);">
                        {{ $rol->usuarios_count }}
                    </span>
                    <span class="td-muted"> usuario(s)</span>
                </td>
                <td style="text-align:center;">
                    <div style="display:flex; gap:.4rem; justify-content:center;">
                        @if($rol->id !== 1)
                        <a href="{{ route('roles.edit', $rol) }}"
                           class="btn btn-sm btn-ghost" title="Editar">✏</a>
                        <form action="{{ route('roles.destroy', $rol) }}" method="POST"
                              onsubmit="return confirm('¿Eliminar el perfil «{{ $rol->nombre }}»?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                    {{ $rol->usuarios_count > 0 ? 'disabled title=Tiene usuarios asignados' : '' }}>
                                🗑
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center; padding:2rem; color:var(--muted);">
                    No hay perfiles registrados.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="table-footer">
        <span>{{ $roles->count() }} perfil(es) en total</span>
    </div>
</div>

@endsection