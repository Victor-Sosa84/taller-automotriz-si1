@extends('layouts.app')
@section('title', 'Perfil del Cliente')

@section('content')

<div style="margin-bottom:1.5rem;">
    <a href="{{ route('clientes.index') }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">
        ← Volver a clientes
    </a>
</div>

{{-- Encabezado --}}
<div style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:1rem; margin-bottom:1.75rem;">
    <div>
        <div style="font-size:.75rem; color:var(--muted); letter-spacing:.08em; text-transform:uppercase; margin-bottom:.3rem;">
            Perfil del cliente
        </div>
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:2rem; font-weight:800; text-transform:uppercase; line-height:1;">
            {{ $cliente->nombre }}
        </h2>
        <div style="font-family:monospace; font-size:.8rem; color:var(--muted); margin-top:.35rem;">
            CI: {{ $cliente->ci }}
        </div>
    </div>
    <a href="{{ route('clientes.edit', $cliente->ci) }}" class="btn btn-ghost">✏ Editar datos</a>
</div>

{{-- Stats generales --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:1rem; margin-bottom:1.75rem;">
    <div class="stat-card">
        <div class="stat-label">Teléfono</div>
        <div style="font-size:1.1rem; font-weight:600; margin-top:.4rem;">{{ $cliente->telefono ?? '—' }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Dirección</div>
        <div style="font-size:1rem; font-weight:500; margin-top:.4rem; color:var(--text);">{{ $cliente->direccion ?? '—' }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Tipo de registro</div>
        <div style="margin-top:.5rem; display:flex; gap:.4rem; flex-wrap:wrap;">
            @if($cliente->es_cliente) <span class="badge badge-recep">Cliente</span> @endif
            @if($cliente->es_personal) <span class="badge badge-mec">Personal</span> @endif
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Visitas registradas</div>
        <div class="stat-value">{{ $historial->count() }}</div>
    </div>
</div>

{{-- Vehículos asociados --}}
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-header">
        <span class="card-title">🚗 Vehículos asociados</span>
        <span style="font-size:.75rem; color:var(--muted);">
            Vehículos que han ingresado con este cliente
        </span>
    </div>

    @if($vehiculos->isNotEmpty())
        <div style="padding:.5rem 0;">
            @foreach($vehiculos as $auto)
            <div style="display:flex; align-items:center; justify-content:space-between;
                        padding:.85rem 1.25rem; border-bottom:1px solid var(--border);">
                <div style="display:flex; align-items:center; gap:1rem;">
                    <span style="font-family:monospace; font-weight:700; color:var(--accent); font-size:1rem;">
                        {{ $auto->placa }}
                    </span>
                    <span style="color:var(--text); font-size:.875rem;">
                        {{ $auto->marca ?? '' }} {{ $auto->modelo ?? '' }}
                        @if($auto->anio) · {{ $auto->anio }} @endif
                        @if($auto->color) · {{ $auto->color }} @endif
                    </span>
                </div>
                <div style="display:flex; gap:.5rem;">
                    <a href="{{ route('historial.show', $auto->placa) }}"
                       class="btn btn-sm btn-ghost">📋 Ver historial</a>
                    <a href="{{ route('autos.edit', $auto->placa) }}"
                       class="btn btn-sm btn-ghost">✏</a>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div style="padding:2rem; text-align:center; color:var(--muted); font-size:.875rem;">
            <div style="font-size:1.75rem; opacity:.3; margin-bottom:.5rem;">🚗</div>
            Este cliente aún no tiene vehículos registrados con diagnóstico.
            <div style="margin-top:.75rem;">
                <a href="{{ route('autos.create') }}" class="btn btn-ghost btn-sm">
                    ＋ Registrar vehículo
                </a>
            </div>
        </div>
    @endif
</div>

{{-- Historial de visitas --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">📋 Historial de visitas</span>
        <span style="font-size:.75rem; color:var(--muted);">
            Diagnósticos registrados para este cliente
        </span>
    </div>

    @if($historial->isNotEmpty())
        <div class="table-wrap" style="border:none; border-radius:0;">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Vehículo</th>
                        <th>Proforma</th>
                        <th>Orden de trabajo</th>
                        <th style="text-align:center;">Ver</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historial as $diag)
                    <tr>
                        <td class="td-muted">{{ $diag->fecha->format('d/m/Y') }}</td>
                        <td>
                            @if($diag->auto)
                                <span style="font-family:monospace; color:var(--accent); font-weight:700;">
                                    {{ $diag->auto->placa }}
                                </span>
                                <span style="color:var(--muted); font-size:.8rem;">
                                    {{ $diag->auto->marca }} {{ $diag->auto->modelo }}
                                </span>
                            @else
                                <span class="td-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($diag->proforma)
                                <span class="badge badge-recep">#{{ $diag->proforma->nro }}</span>
                                <span style="font-size:.78rem; color:var(--muted);">
                                    Bs. {{ number_format($diag->proforma->total_aprox, 2) }}
                                </span>
                            @else
                                <span class="td-muted">Sin proforma</span>
                            @endif
                        </td>
                        <td>
                            @if($diag->proforma?->ordenTrabajo)
                                @php $ot = $diag->proforma->ordenTrabajo; @endphp
                                <span class="badge {{ match($ot->estado) {
                                    'Completada','Finalizada' => 'badge-admin',
                                    'En proceso','Activa'    => 'badge-mec',
                                    default                  => 'badge-recep'
                                } }}">{{ $ot->estado ?? 'Sin estado' }}</span>
                            @else
                                <span class="td-muted">Sin orden</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            @if($diag->auto)
                                <a href="{{ route('historial.show', $diag->auto->placa) }}"
                                   class="btn btn-sm btn-ghost">👁</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="padding:2rem; text-align:center; color:var(--muted); font-size:.875rem;">
            <div style="font-size:1.75rem; opacity:.3; margin-bottom:.5rem;">📋</div>
            Este cliente aún no tiene diagnósticos registrados.
        </div>
    @endif
</div>

@endsection
