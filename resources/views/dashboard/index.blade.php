@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

<div style="margin-bottom:1.5rem;">
    <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; text-transform:uppercase;">
        Bienvenido, {{ auth()->user()->nombre_usuario }}
    </h2>
    <p style="color:var(--muted); font-size:.85rem; margin-top:.2rem;">
        {{ auth()->user()->nombre_rol }} — {{ now()->format('d/m/Y') }}
    </p>
</div>

{{-- Stats visibles según permisos --}}
@if($stats['totalUsuarios'] !== null || $stats['totalClientes'] !== null || $stats['totalPersonal'] !== null)
<div class="stats-grid" style="margin-bottom:1.75rem;">
    @if($stats['totalUsuarios'] !== null)
    <div class="stat-card">
        <div class="stat-label">Usuarios del sistema</div>
        <div class="stat-value">{{ $stats['totalUsuarios'] }}</div>
        <div class="stat-sub">Con acceso a la plataforma</div>
    </div>
    @endif
    @if($stats['totalPersonal'] !== null)
    <div class="stat-card">
        <div class="stat-label">Personal registrado</div>
        <div class="stat-value">{{ $stats['totalPersonal'] }}</div>
        <div class="stat-sub">Mecánicos y administrativos</div>
    </div>
    @endif
    @if($stats['totalClientes'] !== null)
    <div class="stat-card">
        <div class="stat-label">Clientes registrados</div>
        <div class="stat-value">{{ $stats['totalClientes'] }}</div>
        <div class="stat-sub">En la base de datos</div>
    </div>
    @endif
</div>
@endif

{{-- Bitácora reciente --}}
@if($ultimasBitacoras->isNotEmpty())
<div class="card">
    <div class="card-header">
        <span class="card-title">📋 Últimas acciones — Bitácora</span>
        <a href="{{ route('bitacora.index') }}" class="btn btn-ghost btn-sm">Ver todo</a>
    </div>
    <div class="table-wrap" style="border:none; border-radius:0;">
        <table>
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>IP</th>
                    <th>Fecha y hora</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ultimasBitacoras as $log)
                <tr>
                    <td style="font-weight:600;">{{ $log->usuario?->nombre_usuario ?? '—' }}</td>
                    <td>{{ $log->accion }}</td>
                    <td class="td-muted">{{ $log->ip_equipo ?? '—' }}</td>
                    <td class="td-muted">{{ $log->fecha_hora->format('d/m/Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div class="card">
    <div class="card-body" style="text-align:center; padding:3rem;">
        <div style="font-size:3rem; margin-bottom:1rem; opacity:.3;">⚙</div>
        <div style="font-family:'Barlow Condensed',sans-serif; font-size:1.3rem; font-weight:700; text-transform:uppercase; margin-bottom:.5rem;">
            Sistema listo
        </div>
        <div style="color:var(--muted); font-size:.9rem;">
            Usa el menú lateral para navegar entre los módulos disponibles.
        </div>
    </div>
</div>
@endif

@endsection
