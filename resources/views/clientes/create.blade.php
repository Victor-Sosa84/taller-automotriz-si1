@extends('layouts.app')
@section('title', 'Nuevo Cliente')

@section('content')
<div style="max-width:700px;">
    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('clientes.index') }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">
            ← Volver a clientes
        </a>
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; text-transform:uppercase; margin-top:.5rem;">
            Nuevo Cliente
        </h2>
    </div>

    @include('clientes._form', [
        'action'  => route('clientes.store'),
        'method'  => 'POST',
        'cliente' => null,
    ])
</div>
@endsection
