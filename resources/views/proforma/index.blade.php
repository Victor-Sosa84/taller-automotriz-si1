@extends('layouts.app')
@section('title', 'Proformas')
@section('content')
<div>
    <div style="margin-bottom:1.5rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
        <div>
            <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800;">Proformas</h2>
            <p style="color:var(--muted); font-size:.9rem; margin-top:.25rem;">Listado de proformas registradas en el sistema.</p>
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('proforma.index') }}" style="margin-bottom:1.5rem;">
        <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr auto; gap:.75rem; align-items:end;">
            <div class="field-group">
                <label>Estado</label>
                <select name="estado" style="width:100%; box-sizing:border-box;">
                    <option value="">Todos</option>
                    @foreach(['Borrador','Emitida','Aprobada','Observada','Anulada'] as $e)
                        <option value="{{ $e }}" {{ request('estado') === $e ? 'selected' : '' }}>{{ $e }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field-group">
                <label>Desde</label>
                <input type="date" name="desde" value="{{ request('desde') }}" style="width:100%; box-sizing:border-box;">
            </div>
            <div class="field-group">
                <label>Hasta</label>
                <input type="date" name="hasta" value="{{ request('hasta') }}" style="width:100%; box-sizing:border-box;">
            </div>
            <div class="field-group">
                <label>Placa</label>
                <input type="text" name="placa" value="{{ request('placa') }}" placeholder="Ej. 3046FIJ" style="width:100%; box-sizing:border-box;">
            </div>
            <div style="display:flex; gap:.5rem;">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="{{ route('proforma.index') }}" class="btn btn-ghost">Limpiar</a>
            </div>
        </div>
    </form>

    {{-- Tabla --}}
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nro</th>
                    <th>Fecha</th>
                    <th>Vehículo</th>
                    <th>Cliente CI</th>
                    <th>Plazo</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proformas as $p)
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
                    <tr>
                        <td><strong>#{{ $p->nro }}</strong></td>
                        <td class="td-muted">{{ $p->fecha?->format('d/m/Y') }}</td>
                        <td>
                            <span style="color:var(--accent); font-weight:700;">
                                {{ $p->diagnostico->auto->placa ?? '—' }}
                            </span>
                        </td>
                        <td class="td-muted">{{ $p->ci_cliente }}</td>
                        <td class="td-muted">
                            {{ $p->plazo ? \Carbon\Carbon::parse($p->plazo)->format('d/m/Y') : '—' }}
                        </td>
                        <td><strong>Bs {{ number_format($p->total_aprox, 2) }}</strong></td>
                        <td>
                            <span style="font-size:.7rem; font-weight:700; padding:.2rem .6rem; border-radius:999px; {{ $estilo }}">
                                {{ $p->estado }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('proforma.show', $p->nro) }}?from=index" class="btn btn-ghost btn-sm">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center; color:var(--muted); padding:2rem;">
                            No se encontraron proformas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="table-footer">
            <span>{{ $proformas->total() }} proforma(s) encontrada(s)</span>
            {{ $proformas->links() }}
        </div>
    </div>
</div>
@endsection