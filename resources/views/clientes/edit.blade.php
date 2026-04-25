@extends('layouts.app')
@section('title', 'Editar Cliente')

@section('content')
<div style="max-width:700px;">
    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('clientes.index') }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">
            ← Volver a clientes
        </a>
        <div style="display:flex; align-items:baseline; gap:1rem; margin-top:.5rem;">
            <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; text-transform:uppercase;">
                Editar — {{ $cliente->nombre }}
            </h2>
            <span style="font-size:.75rem; color:var(--muted); font-family:monospace;">
                CI: {{ $cliente->ci }}
            </span>
        </div>
    </div>

    @include('clientes._form', [
        'action'  => route('clientes.update', $cliente->ci),
        'method'  => 'PUT',
        'cliente' => $cliente,
    ])
</div>
@endsection
