@extends('layouts.app')
@section('title', 'Nuevo Usuario')

@section('content')

<div style="max-width:820px;">
    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('usuarios.index') }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">
            ← Volver a usuarios
        </a>
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; text-transform:uppercase; margin-top:.5rem;">
            Nuevo Usuario
        </h2>
        <p style="color:var(--muted); font-size:.85rem; margin-top:.2rem;">
            Se creará primero el registro de persona y luego las credenciales de acceso.
        </p>
    </div>

    @include('usuarios._form', [
        'action'  => route('usuarios.store'),
        'method'  => 'POST',
        'usuario' => null,
        'roles'   => $roles,
    ])
</div>

@endsection
