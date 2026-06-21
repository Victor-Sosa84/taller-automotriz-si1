@extends('layouts.app')
@section('title', 'Orden de Trabajo #' . $orden->nro)

@section('content')
<div style="max-width:760px;">
    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('orden_trabajo.index') }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">← Volver a órdenes</a>
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; margin-top:.5rem;">
            Orden de Trabajo #{{ $orden->nro }}
        </h2>
        <p style="color:var(--muted); font-size:.95rem; margin-top:.25rem;">Detalle completo de la orden de trabajo.</p>
    </div>

    <div class="form-card">
        <div class="form-grid">

            <div class="field-group">
                <label>Estado</label>
                @php
                $badgeStyle = match($orden->estado) {
                    'Pendiente de Diagnóstico' => 'background:rgba(245,166,35,.12);color:var(--accent);border:1px solid rgba(245,166,35,.25);',
                    'Diagnóstico Finalizado'   => 'background:rgba(52,152,219,.12);color:#5dade2;border:1px solid rgba(52,152,219,.25);',
                    'En Proceso'               => 'background:rgba(52,152,219,.12);color:#5dade2;border:1px solid rgba(52,152,219,.25);',
                    'Finalizada'               => 'background:rgba(46,204,113,.12);color:var(--success);border:1px solid rgba(46,204,113,.25);',
                    'Anulada'                  => 'background:rgba(231,76,60,.12);color:var(--danger);border:1px solid rgba(231,76,60,.25);',
                    default                    => 'background:rgba(107,117,145,.12);color:var(--muted);border:1px solid rgba(107,117,145,.25);',
                };
                @endphp
                <span class="badge" style="{{ $badgeStyle }}; font-size:.9rem; padding:.4rem .8rem; display:inline-block; width:fit-content;">{{ $orden->estado }}</span>
            </div>

            <div class="field-group">
                <label>Placa del Vehículo</label>
                <p style="margin:0; font-weight:600;">{{ $orden->auto->placa ?? '—' }}</p>
                @if($orden->auto)
                    <p style="margin:.25rem 0 0; font-size:.85rem; color:var(--muted);">
                        {{ $orden->auto->marca }} {{ $orden->auto->modelo }} {{ $orden->auto->anio }}
                    </p>
                @endif
            </div>

            <div class="field-group">
                <label>Proforma Vinculada</label>
                <p style="margin:0;">
                    @if($orden->proforma)
                        #{{ $orden->proforma->nro }} —
                        <span class="badge" style="background:rgba(46,204,113,.12);color:var(--success);border:1px solid rgba(46,204,113,.25);">{{ $orden->proforma->estado }}</span>
                    @else
                        <span style="color:var(--muted);">Sin proforma</span>
                    @endif
                </p>
            </div>

            <div class="field-group">
                <label>Cliente</label>
                <p style="margin:0;">{{ $orden->proforma->cliente->nombre ?? '—' }}</p>
            </div>

            <div class="field-group">
                <label>Total Aproximado</label>
                <p style="margin:0;">{{ $orden->proforma ? 'Bs. ' . number_format($orden->proforma->total_aprox, 2) : '—' }}</p>
            </div>

            <div class="field-group">
                <label>Fecha Inicio</label>
                <p style="margin:0;">{{ $orden->fecha_inicio ? $orden->fecha_inicio->format('d/m/Y H:i') : '—' }}</p>
            </div>

            <div class="field-group">
                <label>Total Real</label>
                <p style="margin:0;">Bs. {{ number_format($orden->total_real, 2) }}</p>
            </div>

            <div class="field-group">
                <label>Fecha Fin</label>
                <p style="margin:0;">{{ $orden->fecha_fin ? $orden->fecha_fin->format('d/m/Y H:i') : '—' }}</p>
            </div>

            <div class="field-group">
                <label>Kilometraje</label>
                <p style="margin:0;">{{ number_format($orden->kilometraje) }} km</p>
            </div>

            <div class="field-group" style="grid-column:1 / -1;">
                <label>Observación de Entrada</label>
                <p style="margin:0; white-space:pre-line;">{{ $orden->observacion_entrada ?? '—' }}</p>
            </div>

            <div class="field-group" style="grid-column:1 / -1;">
                <label>Observación de Salida</label>
                <p style="margin:0; white-space:pre-line;">{{ $orden->observacion_salida ?? '—' }}</p>
            </div>

        </div>

        <div class="form-actions">
            @if(auth()->user()->puede('CU15_BUS'))
            <a href="{{ route('asignacion.index', $orden->nro) }}" class="btn btn-ghost">Responsables de tareas</a>
            @endif
            @if(auth()->user()->puede('CU16_BUS'))
            <a href="{{ route('detalle_ot.index', $orden->nro) }}" class="btn btn-ghost">Detalles de Repuestos y Mano de Obra</a>
            @endif
            @if($orden->puede_editarse)
                @if(auth()->user()->puede('CU14_MOD'))
                <a href="{{ route('orden_trabajo.edit', $orden->nro) }}" class="btn btn-primary">Cerrar Orden</a>
                @endif
            @elseif($orden->estado === 'Finalizada' && !$orden->factura)
                @if(auth()->user()->puede('CU17_GEN'))
                <a href=# class="btn btn-primary">Generar Factura Final</a>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection