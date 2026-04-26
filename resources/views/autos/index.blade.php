@extends('layouts.app')
@section('title', 'Vehículos')

@section('content')

<div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
    <div>
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; text-transform:uppercase;">
            Vehículos
        </h2>
        <p style="color:var(--muted); font-size:.85rem; margin-top:.2rem;">
            Fichas técnicas de vehículos registrados en el taller.
        </p>
    </div>
    <a href="{{ route('autos.create') }}" class="btn btn-primary">＋ Nuevo vehículo</a>
</div>

{{-- Búsqueda --}}
<form method="GET" style="display:flex; gap:.75rem; margin-bottom:1.25rem; flex-wrap:wrap;">
    <input type="text" name="search"
           placeholder="🔍  Buscar por placa, marca, modelo o color..."
           value="{{ request('search') }}"
           style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius);
                  color:var(--text); font-family:'Barlow',sans-serif; font-size:.875rem;
                  padding:.55rem .9rem; outline:none; min-width:300px;">
    <button type="submit" class="btn btn-ghost">Filtrar</button>
    @if(request('search'))
        <a href="{{ route('autos.index') }}" class="btn btn-ghost">✕ Limpiar</a>
    @endif
</form>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Placa</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Año</th>
                <th>Color</th>
                <th style="text-align:center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($autos as $auto)
            <tr>
                <td style="font-family:monospace; font-weight:700; letter-spacing:.05em; color:var(--accent);">
                    {{ $auto->placa }}
                </td>
                <td>{{ $auto->marca ?? '—' }}</td>
                <td>{{ $auto->modelo ?? '—' }}</td>
                <td class="td-muted">{{ $auto->anio ?? '—' }}</td>
                <td class="td-muted">{{ $auto->color ?? '—' }}</td>
                <td style="text-align:center;">
                    <div style="display:flex; gap:.4rem; justify-content:center;">
                        <a href="{{ route('autos.show', $auto->placa) }}"
                           class="btn btn-sm btn-ghost" title="Ver historial">👁</a>
                        <a href="{{ route('autos.edit', $auto->placa) }}"
                           class="btn btn-sm btn-ghost" title="Editar">✏</a>
                        <form action="{{ route('autos.destroy', $auto->placa) }}"
                              method="POST"
                              onsubmit="return confirm('¿Eliminar el vehículo {{ $auto->placa }}? Solo es posible si no tiene diagnósticos.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">🗑</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center; padding:2.5rem; color:var(--muted);">
                    No se encontraron vehículos.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="table-footer">
        <span>{{ $autos->total() }} vehículo(s) registrados</span>
        <div>{{ $autos->links() }}</div>
    </div>
</div>

@endsection
