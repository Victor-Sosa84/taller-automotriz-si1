@extends('layouts.app')
@section('title', 'Usuarios')

@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <div>
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; text-transform:uppercase;">
            Gestión de Usuarios
        </h2>
        <p style="color:var(--muted); font-size:.85rem; margin-top:.2rem;">
            Solo el administrador puede crear, editar o eliminar usuarios.
        </p>
    </div>
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary">＋ Nuevo usuario</a>
</div>

{{-- Filtros --}}
<form method="GET" style="display:flex; gap:.75rem; margin-bottom:1.25rem; flex-wrap:wrap;">
    <input type="text" name="search" placeholder="🔍  Nombre, usuario o CI..."
           value="{{ request('search') }}"
           style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); font-family:'Barlow',sans-serif; font-size:.875rem; padding:.55rem .9rem; outline:none; min-width:260px;">

    <select name="rol"
            style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); font-family:'Barlow',sans-serif; font-size:.875rem; padding:.55rem .9rem; outline:none;">
        <option value="">Todos los roles</option>
        @foreach($roles as $rol)
            <option value="{{ $rol->id }}" {{ request('rol') == $rol->id ? 'selected' : '' }}>
                {{ $rol->nombre }}
            </option>
        @endforeach
    </select>

    <button type="submit" class="btn btn-ghost">Filtrar</button>
    @if(request('search') || request('rol'))
        <a href="{{ route('usuarios.index') }}" class="btn btn-ghost">✕ Limpiar</a>
    @endif
</form>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre completo</th>
                <th>Usuario</th>
                <th>Correo</th>
                <th>CI</th>
                <th>Rol</th>
                <th style="text-align:center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($usuarios as $u)
            <tr>
                <td class="td-muted">#{{ $u->id_usuario }}</td>
                <td style="font-weight:600;">{{ $u->persona?->nombre ?? '—' }}</td>
                <td>{{ $u->nombre_usuario }}</td>
                <td class="td-muted">{{ $u->correo ?? '—' }}</td>
                <td class="td-muted">{{ $u->ci_personal }}</td>
                <td>
                    @php
                        $badgeClass = match($u->id_rol) { 1 => 'badge-admin', 2 => 'badge-mec', 3 => 'badge-recep', default => '' };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $u->rol?->nombre ?? '—' }}</span>
                </td>
                <td style="text-align:center;">
                    <div style="display:flex; gap:.4rem; justify-content:center;">
                        <a href="{{ route('usuarios.edit', $u->id_usuario) }}"
                           class="btn btn-sm btn-ghost" title="Editar">✏</a>

                        @if($u->id_usuario !== auth()->user()->id_usuario)
                        <form action="{{ route('usuarios.destroy', $u->id_usuario) }}"
                              method="POST"
                              onsubmit="return confirm('¿Eliminar al usuario «{{ $u->nombre_usuario }}»?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">🗑</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center; padding:2.5rem; color:var(--muted);">
                    No se encontraron usuarios con esos criterios.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="table-footer">
        <span>{{ $usuarios->total() }} usuario(s) en total</span>
        <div>{{ $usuarios->links() }}</div>
    </div>
</div>

@endsection
