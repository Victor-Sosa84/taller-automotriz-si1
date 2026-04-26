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
    <a href="{{ route('autos.edit', $auto->placa) }}" class="btn btn-ghost">✏ Editar ficha</a>
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
            @if($auto->diagnosticos->isNotEmpty())
                {{ $auto->diagnosticos->sortByDesc('fecha')->first()->fecha->format('d/m/Y') }}
            @else
                Sin registros
            @endif
        </div>
    </div>
</div>

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
            <div style="font-size:.8rem; color:var(--muted);">
                Cliente: <strong style="color:var(--text);">{{ $diag->persona?->nombre ?? '—' }}</strong>
                · CI: {{ $diag->ci_personal }}
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
                {{ $det->descripcion }}
            </div>
            @endforeach
        </div>
        @endif

        {{-- Órdenes de trabajo asociadas --}}
        @if($diag->proforma && $diag->proforma->ordenTrabajo)
            @php $orden = $diag->proforma->ordenTrabajo; @endphp
            <div style="background:rgba(245,166,35,.05); border:1px solid rgba(245,166,35,.15); border-radius:6px; padding:.9rem 1rem;">
                <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem;">
                    <div style="font-family:'Barlow Condensed',sans-serif; font-weight:700; font-size:.95rem;">
                        🔧 Orden de Trabajo #{{ $orden->nro }}
                    </div>
                    <span class="badge {{ match($orden->estado) {
                        'Completada' => 'badge-admin',
                        'En proceso' => 'badge-mec',
                        default      => 'badge-recep'
                    } }}">
                        {{ $orden->estado ?? 'Sin estado' }}
                    </span>
                </div>
                <div style="display:flex; gap:1.5rem; margin-top:.6rem; flex-wrap:wrap; font-size:.82rem; color:var(--muted);">
                    <span>Inicio: {{ \Carbon\Carbon::parse($orden->fecha_inicio)->format('d/m/Y') }}</span>
                    @if($orden->fecha_fin)
                        <span>Fin: {{ \Carbon\Carbon::parse($orden->fecha_fin)->format('d/m/Y') }}</span>
                    @endif
                    @if($orden->kilometraje)
                        <span>Km: {{ number_format($orden->kilometraje) }}</span>
                    @endif
                </div>
                @if($orden->observacion_entrada)
                <div style="margin-top:.6rem; font-size:.82rem; color:var(--muted);">
                    <span style="color:var(--text); font-weight:600;">Entrada:</span> {{ $orden->observacion_entrada }}
                </div>
                @endif
                @if($orden->observacion_salida)
                <div style="margin-top:.3rem; font-size:.82rem; color:var(--muted);">
                    <span style="color:var(--text); font-weight:600;">Salida:</span> {{ $orden->observacion_salida }}
                </div>
                @endif
            </div>
        @else
            <div style="font-size:.8rem; color:var(--muted); font-style:italic;">
                Sin proforma ni orden de trabajo generada aún.
            </div>
        @endif

    </div>
    @empty
    <div style="padding:2.5rem; text-align:center; color:var(--muted); font-size:.9rem;">
        Este vehículo aún no tiene diagnósticos registrados.
    </div>
    @endforelse
</div>

@endsection
