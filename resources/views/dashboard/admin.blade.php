@extends('layouts.app')
@section('title', 'Dashboard — Administrador')

@section('content')

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Usuarios del sistema</div>
        <div class="stat-value">{{ $totalUsuarios }}</div>
        <div class="stat-sub">Con acceso a la plataforma</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Personal registrado</div>
        <div class="stat-value">{{ $totalPersonal }}</div>
        <div class="stat-sub">Mecánicos y administrativos</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Clientes registrados</div>
        <div class="stat-value">{{ $totalClientes }}</div>
        <div class="stat-sub">En la base de datos</div>
    </div>
</div>

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
                @forelse($ultimasBitacoras as $log)
                <tr>
                    <td>{{ $log->usuario?->nombre_usuario ?? '—' }}</td>
                    <td>{{ $log->accion }}</td>
                    <td class="td-muted">{{ $log->ip_equipo ?? '—' }}</td>
                    <td class="td-muted">{{ $log->fecha_hora->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--muted);">Sin registros.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
