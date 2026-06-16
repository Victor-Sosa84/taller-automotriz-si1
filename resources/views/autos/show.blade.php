@extends('layouts.app')
@section('title', 'Ficha — ' . $auto->placa)

@section('content')

<div style="margin-bottom:1.5rem;">
    <a href="{{ route('autos.index') }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">
        ← Volver a vehículos
    </a>
</div>

{{-- Encabezado --}}
<div style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:1rem; margin-bottom:1.75rem;">
    <div>
        <div style="font-size:.75rem; color:var(--muted); letter-spacing:.08em; text-transform:uppercase; margin-bottom:.3rem;">
            Ficha técnica del vehículo
        </div>
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:2.2rem; font-weight:800; text-transform:uppercase; color:var(--accent); line-height:1;">
            {{ $auto->placa }}
        </h2>
        <div style="font-size:.95rem; color:var(--muted); margin-top:.35rem;">
            {{ $auto->marca ?? '' }} {{ $auto->modelo ?? '' }}
            @if($auto->anio) · {{ $auto->anio }} @endif
            @if($auto->color) · {{ $auto->color }} @endif
        </div>
    </div>
    <div style="display:flex; gap:.75rem; flex-wrap:wrap;">
        <a href="{{ route('autos.edit', $auto->placa) }}" class="btn btn-ghost">✏ Editar ficha</a>
        @if(auth()->user()->puede('CU04_ADD'))
            <a href="{{ route('orden-trabajo.create', ['placa' => $auto->placa]) }}" class="btn btn-primary">🛠 Registrar ingreso</a>
        @endif
    </div>
</div>

{{-- Stats rápidos --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px,1fr)); gap:1rem; margin-bottom:1.75rem;">
    <div class="stat-card">
        <div class="stat-label">Total diagnósticos</div>
        <div class="stat-value">{{ $auto->diagnosticos->count() }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Órdenes de trabajo</div>
        <div class="stat-value">
            {{ $auto->diagnosticos->flatMap(fn($d) => $d->ordenTrabajo ?? collect())->count() }}
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

@if($auto->ordenesPendientes->isNotEmpty())
    @foreach($auto->ordenesPendientes as $op)
    <div style="background:rgba(245,166,35,.08); border:1px solid rgba(245,166,35,.3); border-radius:6px; padding:.9rem 1rem; margin-bottom:1rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem;">
        <div>
            <span style="font-weight:700; color:var(--accent);">⚠ Ingreso pendiente de diagnóstico</span>
            <span class="td-muted" style="margin-left:.75rem;">Orden #{{ $op->nro }} — {{ \Carbon\Carbon::parse($op->fecha_inicio)->format('d/m/Y') }}</span>
        </div>
        @if(auth()->user()->puede('CU05_ADD'))
            <a href="{{ route('diagnostico.create', ['orden_id' => $op->nro, 'from' => 'auto']) }}" class="btn btn-primary btn-sm">
                Continuar diagnóstico →
            </a>
        @endif
    </div>
    @endforeach
@endif

{{-- Historial de diagnósticos --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">📋 Historial de diagnósticos y órdenes</span>
    </div>

    @forelse($auto->diagnosticos->sortByDesc('fecha') as $diag)
    <div style="border-bottom:1px solid var(--border); padding:1.25rem;">

        {{-- Cabecera del diagnóstico --}}
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem; margin-bottom:.75rem;">
            <div style="display:flex; align-items:center; gap:.75rem;">
                <span style="font-family:'Barlow Condensed',sans-serif; font-weight:700; font-size:1rem; color:var(--accent);">
                    Diagnóstico #{{ $diag->id }}
                </span>
                <span class="td-muted">{{ \Carbon\Carbon::parse($diag->fecha)->format('d/m/Y H:i') }}</span>
            </div>
            <div style="display:flex; align-items:center; gap:1rem; flex-wrap:wrap;">
                <div style="font-size:.8rem; color:var(--muted);">
                    Registrado por: <strong style="color:var(--text);">{{ $diag->persona?->nombre ?? '—' }}</strong>
                </div>
                <a href="{{ route('diagnostico.show', $diag->id) }}" class="btn btn-ghost btn-sm">Ver →</a>
            </div>
        </div>

        {{-- Detalles del diagnóstico --}}
        @if($diag->detalles && $diag->detalles->isNotEmpty())
        <div style="margin-bottom:.75rem;">
            <div style="font-size:.75rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:.4rem;">
                Observaciones del diagnóstico
            </div>
            @foreach($diag->detalles as $det)
            <div style="background:var(--surface2); border-radius:4px; padding:.5rem .75rem; font-size:.875rem; margin-bottom:.3rem;">
                {{ $det->falla }}
            </div>
            @endforeach

            @if($diag->descripcion)
            <div style="font-size:.75rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-top:.75rem; margin-bottom:.4rem;">
                Dictamen
            </div>
            <div style="background:var(--surface2); border-radius:4px; padding:.5rem .75rem; font-size:.875rem;">
                {{ $diag->descripcion }}
            </div>
            @endif
        </div>
        @endif

        {{-- Proforma asociada y órdenes de trabajo --}}
        @if($diag->proforma)
            @php $p = $diag->proforma; @endphp
            @php
                $colores = [
                    'Borrador'  => 'background:rgba(107,117,145,.2); color:var(--muted);',
                    'Emitida'   => 'background:rgba(52,152,219,.15); color:#5dade2;',
                    'Aprobada'  => 'background:rgba(46,204,113,.15); color:var(--success);',
                    'Observada' => 'background:rgba(245,166,35,.15); color:var(--accent);',
                    'Anulada'   => 'background:rgba(231,76,60,.1); color:var(--danger);',
                ];
                $estilo = $colores[$p->estado] ?? '';
            @endphp
            <div style="background:rgba(245,166,35,.05); border:1px solid rgba(245,166,35,.15); border-radius:6px; padding:.9rem 1rem; margin-top:.5rem;">
                <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem;">
                    <div style="font-family:'Barlow Condensed',sans-serif; font-weight:700; font-size:.95rem;">
                        📄 Proforma #{{ $p->nro }}
                    </div>
                    <div style="display:flex; align-items:center; gap:.75rem;">
                        <span style="font-size:.7rem; font-weight:700; padding:.2rem .6rem; border-radius:999px; {{ $estilo }}">
                            {{ $p->estado }}
                        </span>
                        <a href="{{ route('proforma.show', $p->nro) }}" class="btn btn-ghost btn-sm">Ver →</a>
                    </div>
                </div>
                <div style="font-size:.82rem; color:var(--muted); margin-top:.5rem;">
                    Total: <strong style="color:var(--accent);">Bs {{ number_format($p->total_aprox, 2) }}</strong>
                    @if($p->plazo) · Plazo: {{ \Carbon\Carbon::parse($p->plazo)->format('d/m/Y') }} @endif
                </div>
            </div>
            @if($diag->proforma->ordenTrabajo)
                @php $orden = $diag->proforma->ordenTrabajo; @endphp
                <div style="background:rgba(245,166,35,.05); border:1px solid rgba(245,166,35,.15); border-radius:6px; padding:.9rem 1rem; margin-top:.5rem;">
                    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem;">
                        <div style="font-family:'Barlow Condensed',sans-serif; font-weight:700; font-size:.95rem;">
                            🔧 Orden de Trabajo #{{ $orden->nro }}
                        </div>
                        <span class="badge">{{ $orden->estado ?? 'Sin estado' }}</span>
                    </div>
                    <div style="font-size:.82rem; color:var(--muted); margin-top:.5rem;">
                        Inicio: {{ \Carbon\Carbon::parse($orden->fecha_inicio)->format('d/m/Y') }}
                        @if($orden->kilometraje) · Km: {{ number_format($orden->kilometraje) }} @endif
                    </div>
                </div>
            @endif
        @else
            <div style="font-size:.8rem; color:var(--muted); font-style:italic;">
                Sin proforma generada aún.
            </div>
        @endif

    </div>
    @empty
    @if($auto->ordenesPendientes->isEmpty())
    <div style="padding:2.5rem; text-align:center; color:var(--muted); font-size:.9rem;">
        Este vehículo aún no tiene diagnósticos registrados.
    </div>
    @endif
    @endforelse
</div>

@endsection
