@extends('layouts.app')
@section('title', 'Clientes')

@section('content')

<div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
    <div>
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; text-transform:uppercase;">
            Clientes
        </h2>
        <p style="color:var(--muted); font-size:.85rem; margin-top:.2rem;">
            Registro de clientes del taller. Los clientes no tienen acceso al sistema.
        </p>
    </div>
    <a href="{{ route('clientes.create') }}" class="btn btn-primary">＋ Nuevo cliente</a>
</div>

{{-- Búsqueda --}}
<form method="GET" style="display:flex; gap:.75rem; margin-bottom:1.25rem; flex-wrap:wrap;">
    <input type="text" name="search"
           placeholder="🔍  Buscar por nombre, CI o teléfono..."
           value="{{ request('search') }}"
           style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius);
                  color:var(--text); font-family:'Barlow',sans-serif; font-size:.875rem;
                  padding:.55rem .9rem; outline:none; min-width:280px;">
    <button type="submit" class="btn btn-ghost">Filtrar</button>
    @if(request('search'))
        <a href="{{ route('clientes.index') }}" class="btn btn-ghost">✕ Limpiar</a>
    @endif
</form>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>CI</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Es personal</th>
                <th style="text-align:center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clientes as $cliente)
            <tr>
                <td class="td-muted" style="font-family:monospace;">{{ $cliente->ci }}</td>
                <td style="font-weight:600;">{{ $cliente->nombre }}</td>
                <td class="td-muted">{{ $cliente->telefono ?? '—' }}</td>
                <td class="td-muted">{{ $cliente->direccion ?? '—' }}</td>
                <td>
                    @if($cliente->es_personal)
                        <span class="badge badge-mec">También personal</span>
                    @else
                        <span style="color:var(--muted); font-size:.8rem;">—</span>
                    @endif
                </td>
                <td style="text-align:center;">
                    <div style="display:flex; gap:.4rem; justify-content:center;">
                        <a href="{{ route('clientes.show', $cliente->ci) }}"
                           class="btn btn-sm btn-ghost" title="Ver perfil">👁</a>
                        <a href="{{ route('clientes.edit', $cliente->ci) }}"
                           class="btn btn-sm btn-ghost" title="Editar">✏</a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center; padding:2.5rem; color:var(--muted);">
                    No se encontraron clientes.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="table-footer">
        <span>{{ $clientes->total() }} cliente(s) registrados</span>
        <div>{{ $clientes->links() }}</div>
    </div>
</div>

@endsection
