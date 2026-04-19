@extends('layouts.app')
@section('title', 'Bitácora')

@section('content')

<div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
    <div>
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; text-transform:uppercase;">
            Bitácora del Sistema
        </h2>
        <p style="color:var(--muted); font-size:.85rem; margin-top:.2rem;">
            Registro de inicios de sesión, cierres y gestión de usuarios.
        </p>
    </div>
</div>

{{-- Filtros --}}
<form method="GET" style="display:flex; gap:.75rem; margin-bottom:1.25rem; flex-wrap:wrap;">
    <input type="text" name="usuario" placeholder="🔍  Nombre de usuario..."
           value="{{ request('usuario') }}"
           style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); font-family:'Barlow',sans-serif; font-size:.875rem; padding:.55rem .9rem; outline:none; min-width:220px;">

    <input type="text" name="accion" placeholder="Acción..."
           value="{{ request('accion') }}"
           style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); font-family:'Barlow',sans-serif; font-size:.875rem; padding:.55rem .9rem; outline:none; min-width:180px;">

    <input type="date" name="fecha"
           value="{{ request('fecha') }}"
           style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); color:var(--text); font-family:'Barlow',sans-serif; font-size:.875rem; padding:.55rem .9rem; outline:none;">

    <button type="submit" class="btn btn-ghost">Filtrar</button>
    @if(request('usuario') || request('accion') || request('fecha'))
        <a href="{{ route('bitacora.index') }}" class="btn btn-ghost">✕ Limpiar</a>
    @endif
</form>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Usuario</th>
                <th>Acción</th>
                <th>IP del equipo</th>
                <th>Fecha y hora</th>
            </tr>
        </thead>
        <tbody>
            @forelse($registros as $log)
            <tr>
                <td class="td-muted">{{ $log->id }}</td>
                <td style="font-weight:600;">{{ $log->usuario?->nombre_usuario ?? '—' }}</td>
                <td>
                    @php
                        $color = match(true) {
                            str_contains($log->accion, 'Inicio')    => 'color:var(--success)',
                            str_contains($log->accion, 'Cierre') || str_contains($log->accion, 'Cerrar') => 'color:var(--muted)',
                            str_contains($log->accion, 'Registro')  => 'color:var(--accent)',
                            str_contains($log->accion, 'Eliminaci') => 'color:var(--danger)',
                            default => ''
                        };
                    @endphp
                    <span style="{{ $color }}">{{ $log->accion }}</span>
                </td>
                <td class="td-muted">{{ $log->ip_equipo ?? '—' }}</td>
                <td class="td-muted">{{ $log->fecha_hora->format('d/m/Y H:i:s') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center; padding:2.5rem; color:var(--muted);">
                    No hay registros con esos criterios.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="table-footer">
        <span>{{ $registros->total() }} registro(s)</span>
        <div>{{ $registros->links() }}</div>
    </div>
</div>

@endsection
