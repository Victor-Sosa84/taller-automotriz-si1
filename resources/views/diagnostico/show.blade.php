@extends('layouts.app')
@section('title', 'Diagnóstico #' . $diagnostico->id)
@section('content')
<div style="max-width:860px;">

    {{-- Header --}}
    <div style="margin-bottom:1.5rem; display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
        <div>
            <a href="{{ route('autos.show', $diagnostico->placa_auto) }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">← Volver a vehículo</a>
            <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; margin-top:.5rem;">
                Diagnóstico #{{ $diagnostico->id }}
            </h2>
            <p style="color:var(--muted); font-size:.9rem; margin-top:.25rem;">
                {{ $diagnostico->fecha?->format('d/m/Y H:i') }} —
                Vehículo <strong style="color:var(--accent);">{{ $diagnostico->placa_auto }}</strong> —
                Técnico: {{ $diagnostico->persona->nombre ?? '—' }}
            </p>
        </div>
        @if(!$diagnostico->proforma)
            <a href="{{ route('proforma.create', ['diagnostico_id' => $diagnostico->id]) }}"
                class="btn btn-primary">+ Elaborar Proforma</a>
        @else
            <a href="{{ route('proforma.show', $diagnostico->proforma->nro) }}"
                class="btn btn-ghost">Ver Proforma #{{ $diagnostico->proforma->nro }}</a>
        @endif
    </div>

    {{-- Descripción --}}
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header"><span class="card-title">Descripción del diagnóstico</span></div>
        <div class="card-body">
            <p style="line-height:1.6;">{{ $diagnostico->descripcion }}</p>
        </div>
    </div>

    {{-- Fallas --}}
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header"><span class="card-title">Fallas encontradas</span></div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>#</th><th>Falla</th></tr>
                </thead>
                <tbody>
                    @forelse($diagnostico->detalles as $detalle)
                        <tr>
                            <td class="td-muted">{{ $loop->iteration }}</td>
                            <td>{{ $detalle->falla }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" style="color:var(--muted); text-align:center;">Sin fallas registradas</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Proforma vinculada --}}
    @if($diagnostico->proforma)
        @php $p = $diagnostico->proforma; @endphp
        @php
            $colores = [
                'Borrador'  => 'background:rgba(107,117,145,.2); color:var(--muted);',
                'Emitida'   => 'background:rgba(52,152,219,.15); color:#5dade2;',
                'Aprobada'  => 'background:rgba(46,204,113,.15); color:var(--success);',
                'Observada' => 'background:rgba(231,76,60,.15); color:var(--danger);',
                'Anulada' => 'background:rgba(231,76,60,.1); color:var(--danger);',
            ];
            $estilo = $colores[$p->estado] ?? '';
        @endphp
        <div class="card">
            <div class="card-header" style="justify-content:space-between;">
                <span class="card-title">Proforma vinculada</span>
                <span style="font-size:.75rem; font-weight:700; padding:.2rem .65rem; border-radius:999px; {{ $estilo }}">
                    {{ $p->estado }}
                </span>
            </div>
            <div class="card-body">
                <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:1rem;">
                    <div><strong>Nro:</strong> #{{ $p->nro }}</div>
                    <div><strong>Fecha:</strong> {{ $p->fecha?->format('d/m/Y') }}</div>
                    <div><strong>Total:</strong> <span style="color:var(--accent); font-weight:700;">Bs {{ number_format($p->total_aprox, 2) }}</span></div>
                </div>
                <a href="{{ route('proforma.show', $p->nro) }}" class="btn btn-ghost btn-sm">Ver detalle completo →</a>
            </div>
        </div>
    @endif

</div>
@endsection