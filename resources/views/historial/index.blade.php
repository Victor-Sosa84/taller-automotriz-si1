@extends('layouts.app')
@section('title', 'Historial de Mantenimiento')

@section('content')

<div style="margin-bottom:1.75rem;">
    <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; text-transform:uppercase;">
        Historial de Mantenimiento
    </h2>
    <p style="color:var(--muted); font-size:.85rem; margin-top:.2rem;">
        Busca un vehículo por placa, marca o modelo para ver su historial completo.
    </p>
</div>

{{-- Búsqueda --}}
<form method="GET" style="display:flex; gap:.75rem; margin-bottom:1.75rem; flex-wrap:wrap;">
    <input type="text" name="search"
           placeholder="🔍  Placa, marca o modelo..."
           value="{{ $search ?? '' }}"
           style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius);
                  color:var(--text); font-family:'Barlow',sans-serif; font-size:.875rem;
                  padding:.65rem 1rem; outline:none; min-width:300px; font-size:1rem;"
           autofocus>
    <button type="submit" class="btn btn-primary">Buscar</button>
    @if($search)
        <a href="{{ route('historial.index') }}" class="btn btn-ghost">✕ Limpiar</a>
    @endif
</form>

@if($search)
    {{-- Resultados --}}
    @if($autos->count() > 0)
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Placa</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Año</th>
                        <th>Color</th>
                        <th style="text-align:center;">Ver historial</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($autos as $auto)
                    <tr>
                        <td style="font-family:monospace; font-weight:700; color:var(--accent);">
                            {{ $auto->placa }}
                        </td>
                        <td>{{ $auto->marca ?? '—' }}</td>
                        <td>{{ $auto->modelo ?? '—' }}</td>
                        <td class="td-muted">{{ $auto->anio ?? '—' }}</td>
                        <td class="td-muted">{{ $auto->color ?? '—' }}</td>
                        <td style="text-align:center;">
                            <a href="{{ route('historial.show', $auto->placa) }}"
                               class="btn btn-sm btn-primary">Ver historial →</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="table-footer">
                <span>{{ $autos->total() }} resultado(s)</span>
                <div>{{ $autos->links() }}</div>
            </div>
        </div>
    @else
        <div class="card">
            <div style="padding:2.5rem; text-align:center; color:var(--muted);">
                <div style="font-size:2rem; margin-bottom:.75rem; opacity:.4;">🚗</div>
                <p>No se encontró ningún vehículo con «{{ $search }}».</p>
                <p style="font-size:.82rem; margin-top:.4rem;">
                    Verifica la placa o
                    <a href="{{ route('autos.create') }}" style="color:var(--accent); text-decoration:none;">
                        registra el vehículo
                    </a>.
                </p>
            </div>
        </div>
    @endif
@else
    {{-- Estado vacío inicial --}}
    <div class="card">
        <div style="padding:3rem; text-align:center; color:var(--muted);">
            <div style="font-size:2.5rem; margin-bottom:1rem; opacity:.3;">🔍</div>
            <p style="font-size:1rem;">Ingresa la placa o datos del vehículo para comenzar.</p>
        </div>
    </div>
@endif

@endsection
