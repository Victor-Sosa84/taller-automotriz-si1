@extends('layouts.app')
@section('title', 'Historial — ' . $auto->placa)

@section('content')

<div style="margin-bottom:1.5rem;">
    <a href="{{ route('historial.index') }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">
        ← Volver a búsqueda
    </a>
</div>

{{-- Encabezado del vehículo --}}
<div style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:1rem; margin-bottom:1.75rem;">
    <div>
        <div style="font-size:.75rem; color:var(--muted); letter-spacing:.08em; text-transform:uppercase; margin-bottom:.3rem;">
            Historial de mantenimiento
        </div>
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:2.2rem; font-weight:800; color:var(--accent); line-height:1;">
            {{ $auto->placa }}
        </h2>
        <div style="font-size:.9rem; color:var(--muted); margin-top:.35rem;">
            {{ $auto->marca ?? '' }} {{ $auto->modelo ?? '' }}
            @if($auto->anio) · {{ $auto->anio }} @endif
            @if($auto->color) · {{ $auto->color }} @endif
        </div>
    </div>

    <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
        <a href="{{ route('autos.edit', $auto->placa) }}" class="btn btn-ghost">✏ Editar ficha</a>
    </div>
</div>

{{-- Stats --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:1rem; margin-bottom:1.75rem;">
    <div class="stat-card">
        <div class="stat-label">Diagnósticos</div>
        <div class="stat-value">{{ $auto->diagnosticos->count() }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Órdenes de trabajo</div>
        <div class="stat-value">
            {{ $auto->diagnosticos->filter(fn($d) => $d->proforma?->ordenTrabajo)->count() }}
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Último ingreso</div>
        <div style="font-size:1rem; font-weight:600; color:var(--accent); margin-top:.4rem;">
            @if($auto->ordenesPendientes->isNotEmpty())
                {{ \Carbon\Carbon::parse($auto->ordenesPendientes->sortByDesc('fecha_inicio')->first()->fecha_inicio)->format('d/m/Y') }}
            @elseif($auto->diagnosticos->isNotEmpty())
                {{ $auto->diagnosticos->sortByDesc('fecha')->first()->fecha->format('d/m/Y') }}
            @else
                Sin registros
            @endif
        </div>
    </div>
</div>

{{-- Ingreso pendiente --}}
@if($auto->ordenesPendientes->isNotEmpty())
    @foreach($auto->ordenesPendientes as $op)
    <div style="background:rgba(245,166,35,.08); border:1px solid rgba(245,166,35,.3); border-radius:6px; padding:.9rem 1rem; margin-bottom:1rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem;">
        <div>
            <span style="font-weight:700; color:var(--accent);">⚠ Ingreso pendiente de diagnóstico</span>
            <span class="td-muted" style="margin-left:.75rem;">Orden #{{ $op->nro }} — {{ \Carbon\Carbon::parse($op->fecha_inicio)->format('d/m/Y') }}</span>
        </div>
        @if(auth()->user()->puede('CU05_ADD'))
            <a href="{{ route('diagnostico.create', ['orden_id' => $op->nro, 'from' => 'historial']) }}" class="btn btn-primary btn-sm">
                Continuar diagnóstico →
            </a>
        @endif
    </div>
    @endforeach
@endif

{{-- Timeline de diagnósticos --}}
@forelse($auto->diagnosticos as $diag)

<div class="card" style="margin-bottom:1.25rem;">

    {{-- Header del diagnóstico --}}
    <div class="card-header" style="background:var(--surface2);">
        <div style="display:flex; align-items:center; gap:.75rem; flex-wrap:wrap;">
            <span style="font-family:'Barlow Condensed',sans-serif; font-size:1.1rem; font-weight:800; color:var(--accent);">
                Diagnóstico #{{ $diag->id }}
            </span>
            <span class="td-muted">{{ $diag->fecha->format('d/m/Y H:i') }}</span>
        </div>
        <div style="display:flex; align-items:center; gap:1rem;">
            <div style="font-size:.82rem; color:var(--muted);">
                Registrado por: <strong style="color:var(--text);">{{ $diag->persona?->nombre ?? '—' }}</strong>
            </div>
            <a href="{{ route('diagnostico.show', $diag->id) }}?from=historial" class="btn btn-ghost btn-sm">Ver →</a>
        </div>
    </div>

    <div class="card-body">

        {{-- Detalles del diagnóstico --}}
        @if($diag->detalles->isNotEmpty())
        @php $detalles = $diag->detalles; @endphp
        <div style="margin-bottom:1rem;">
            <div style="font-size:.72rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--muted); margin-bottom:.5rem;">
                Observaciones del diagnóstico
            </div>
            @foreach($diag->detalles as $det)
                @if($det->falla)
                <div style="display:flex; align-items:flex-start; gap:.5rem; padding:.4rem 0;
                            border-bottom:1px solid var(--border); font-size:.875rem;">
                    <span style="color:var(--accent); flex-shrink:0;">›</span>
                    {{ $det->falla }}
                </div>
                @endif
            @endforeach
            @if($diag->descripcion)
            <div style="font-size:.72rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase;
            color:var(--muted); margin-bottom:.5rem; margin-top:.75rem;">
                Dictamen
            </div>
            <div style="display:flex; align-items:flex-start; gap:.5rem; padding:.4rem 0;
                        border-bottom:1px solid var(--border); font-size:.875rem;">
                <span style="color:var(--accent); flex-shrink:0;">›</span>
                {{ $diag->descripcion }}
            </div>
            @endif 
        </div>
        @endif

        {{-- Proforma y Orden de Trabajo --}}
        @if($diag->proforma)
            @php $proforma = $diag->proforma; @endphp
            @php
                $colores = [
                    'Borrador'  => 'background:rgba(107,117,145,.2); color:var(--muted);',
                    'Emitida'   => 'background:rgba(52,152,219,.15); color:#5dade2;',
                    'Aprobada'  => 'background:rgba(46,204,113,.15); color:var(--success);',
                    'Observada' => 'background:rgba(245,166,35,.15); color:var(--accent);',
                    'Anulada'   => 'background:rgba(231,76,60,.1); color:var(--danger);',
                ];
                $estiloP = $colores[$proforma->estado] ?? '';
            @endphp
            <div style="background:rgba(245,166,35,.05); border:1px solid rgba(245,166,35,.15); border-radius:6px; padding:.9rem 1rem; margin-bottom:.75rem;">
                <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem;">
                    <div style="font-family:'Barlow Condensed',sans-serif; font-weight:700; font-size:.95rem;">
                        📄 Proforma #{{ $proforma->nro }}
                    </div>
                    @if($proforma->estado)
                        <span style="font-size:.7rem; font-weight:700; padding:.2rem .6rem; border-radius:999px; {{ $estiloP }}">
                            {{ $proforma->estado }}
                        </span>
                    @endif
                </div>
                <div style="font-size:.82rem; color:var(--muted); margin-top:.5rem;">
                    Total: <strong style="color:var(--accent);">Bs {{ number_format($proforma->total_aprox, 2) }}</strong>
                    · Fecha: {{ $proforma->fecha->format('d/m/Y') }}
                    @if($proforma->plazo) · Plazo: {{ \Carbon\Carbon::parse($proforma->plazo)->format('d/m/Y') }} @endif
                </div>
            </div>

            {{-- Orden de trabajo --}}
            @if($proforma->ordenTrabajo)
                @php $orden = $proforma->ordenTrabajo; @endphp

                <div style="border:1px solid var(--border); border-radius:6px; overflow:hidden;">

                    {{-- Header orden --}}
                    <div style="background:var(--surface2); padding:.75rem 1rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem;">
                        <span style="font-family:'Barlow Condensed',sans-serif; font-weight:700;">
                            🔧 Orden de Trabajo #{{ $orden->nro }}
                        </span>
                        <div style="display:flex; gap:.75rem; font-size:.8rem; color:var(--muted); align-items:center;">
                            <span>{{ $orden->fecha_inicio->format('d/m/Y') }}
                                @if($orden->fecha_fin) → {{ $orden->fecha_fin->format('d/m/Y') }} @endif
                            </span>
                            @if($orden->kilometraje)
                                <span>{{ number_format($orden->kilometraje) }} km</span>
                            @endif
                            <span class="badge {{ match($orden->estado) {
                                'Completada','Finalizada' => 'badge-admin',
                                'En proceso','Activa'     => 'badge-mec',
                                default                   => 'badge-recep'
                            } }}">{{ $orden->estado ?? 'Sin estado' }}</span>
                        </div>
                    </div>

                    <div style="padding:.9rem 1rem;">

                        {{-- Observaciones --}}
                        @if($orden->observacion_entrada || $orden->observacion_salida)
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:.75rem; margin-bottom:1rem;">
                            @if($orden->observacion_entrada)
                            <div>
                                <div style="font-size:.7rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:.3rem;">Entrada</div>
                                <div style="font-size:.85rem;">{{ $orden->observacion_entrada }}</div>
                            </div>
                            @endif
                            @if($orden->observacion_salida)
                            <div>
                                <div style="font-size:.7rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:.3rem;">Salida</div>
                                <div style="font-size:.85rem;">{{ $orden->observacion_salida }}</div>
                            </div>
                            @endif
                        </div>
                        @endif

                        {{-- Trabajos realizados --}}
                        @if($orden->detallesTrabajo->isNotEmpty())
                        <div style="margin-bottom:.75rem;">
                            <div style="font-size:.7rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:.4rem;">Trabajos</div>
                            <table style="width:100%; font-size:.82rem; border-collapse:collapse;">
                                <thead>
                                    <tr style="border-bottom:1px solid var(--border);">
                                        <th style="text-align:left; padding:.3rem .5rem; color:var(--muted); font-weight:600;">Servicio</th>
                                        <th style="text-align:center; padding:.3rem .5rem; color:var(--muted); font-weight:600;">Cant.</th>
                                        <th style="text-align:right; padding:.3rem .5rem; color:var(--muted); font-weight:600;">Costo</th>
                                        <th style="text-align:center; padding:.3rem .5rem; color:var(--muted); font-weight:600;">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orden->detallesTrabajo as $dt)
                                    <tr style="border-bottom:1px solid rgba(42,48,69,.5);">
                                        <td style="padding:.35rem .5rem;">{{ $dt->manoObra?->descripcion ?? '—' }}</td>
                                        <td style="text-align:center; padding:.35rem .5rem;">{{ $dt->cantidad }}</td>
                                        <td style="text-align:right; padding:.35rem .5rem; color:var(--accent);">Bs. {{ number_format($dt->costo, 2) }}</td>
                                        <td style="text-align:center; padding:.35rem .5rem; color:var(--muted);">{{ $dt->estado ?? '—' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif

                        {{-- Repuestos utilizados --}}
                        @if($orden->detallesRepuesto->isNotEmpty())
                        <div>
                            <div style="font-size:.7rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:.4rem;">Repuestos</div>
                            <table style="width:100%; font-size:.82rem; border-collapse:collapse;">
                                <thead>
                                    <tr style="border-bottom:1px solid var(--border);">
                                        <th style="text-align:left; padding:.3rem .5rem; color:var(--muted); font-weight:600;">Repuesto</th>
                                        <th style="text-align:center; padding:.3rem .5rem; color:var(--muted); font-weight:600;">Cant.</th>
                                        <th style="text-align:right; padding:.3rem .5rem; color:var(--muted); font-weight:600;">P. Unit.</th>
                                        <th style="text-align:right; padding:.3rem .5rem; color:var(--muted); font-weight:600;">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orden->detallesRepuesto as $dr)
                                    @php $subtotal = ($dr->cantidad * $dr->precio_unitario) - $dr->descuento; @endphp
                                    <tr style="border-bottom:1px solid rgba(42,48,69,.5);">
                                        <td style="padding:.35rem .5rem;">{{ $dr->repuesto?->nombre ?? '—' }}</td>
                                        <td style="text-align:center; padding:.35rem .5rem;">{{ $dr->cantidad }}</td>
                                        <td style="text-align:right; padding:.35rem .5rem;">Bs. {{ number_format($dr->precio_unitario, 2) }}</td>
                                        <td style="text-align:right; padding:.35rem .5rem; color:var(--accent);">Bs. {{ number_format($subtotal, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif

                    </div>
                </div>
            @endif
        @else
            <div style="font-size:.82rem; color:var(--muted); font-style:italic;">
                Sin proforma ni orden de trabajo generada para este diagnóstico.
            </div>
        @endif

    </div>
</div>

@empty
@if($auto->ordenesPendientes->isEmpty())
<div class="card">
    <div style="padding:2.5rem; text-align:center; color:var(--muted);">
        <div style="font-size:2rem; opacity:.3; margin-bottom:.75rem;">📋</div>
        <p>Este vehículo aún no tiene diagnósticos registrados.</p>
    </div>
</div>
@endif
@endforelse

@endsection
