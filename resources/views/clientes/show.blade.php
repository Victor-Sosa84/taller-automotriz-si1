@extends('layouts.app')
@section('title', 'Perfil del Cliente')

@section('content')

<div style="margin-bottom:1.5rem;">
    <a href="{{ route('clientes.index') }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">
        ← Volver a clientes
    </a>
</div>

{{-- Encabezado del perfil --}}
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

{{-- Datos generales --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px,1fr)); gap:1rem; margin-bottom:1.75rem;">

    <div class="stat-card">
        <div class="stat-label">Teléfono</div>
        <div style="font-size:1.1rem; font-weight:600; margin-top:.4rem;">
            {{ $cliente->telefono ?? '—' }}
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Dirección</div>
        <div style="font-size:1rem; font-weight:500; margin-top:.4rem; color:var(--text);">
            {{ $cliente->direccion ?? '—' }}
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Tipo de registro</div>
        <div style="margin-top:.5rem; display:flex; gap:.4rem; flex-wrap:wrap;">
            @if($cliente->es_cliente)
                <span class="badge badge-recep">Cliente</span>
            @endif
            @if($cliente->es_personal)
                <span class="badge badge-mec">Personal</span>
            @endif
        </div>
    </div>

</div>

{{-- Vehículos del cliente --}}
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-header">
        <span class="card-title">🚗 Vehículos registrados</span>
        {{-- Próximamente: botón para registrar vehículo --}}
        <span style="font-size:.75rem; color:var(--muted);">
            Próximamente — ficha técnica de vehículo
        </span>
    </div>
    <div style="padding:2rem; text-align:center; color:var(--muted); font-size:.875rem;">
        Los vehículos de este cliente aparecerán aquí una vez implementada la ficha técnica.
    </div>
</div>

{{-- Historial de visitas --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">📋 Historial de visitas</span>
        <span style="font-size:.75rem; color:var(--muted);">
            Próximamente — historial de mantenimiento
        </span>
    </div>
    <div style="padding:2rem; text-align:center; color:var(--muted); font-size:.875rem;">
        Los diagnósticos y órdenes de trabajo de este cliente aparecerán aquí.
    </div>
</div>

@endsection
