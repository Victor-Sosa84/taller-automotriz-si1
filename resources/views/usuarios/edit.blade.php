@extends('layouts.app')
@section('title', 'Editar Usuario')

@section('content')

<div style="max-width:820px;">
    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('usuarios.index') }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">
            ← Volver a usuarios
        </a>
        <div style="display:flex; align-items:baseline; gap:1rem; margin-top:.5rem;">
            <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; text-transform:uppercase;">
                Editar — {{ $usuario->nombre_usuario }}
            </h2>
            <span style="font-size:.75rem; color:var(--muted); font-family:monospace;">
                #{{ $usuario->id_usuario }}
            </span>
        </div>
    </div>

    @include('usuarios._form', [
        'action'  => route('usuarios.update', $usuario->id_usuario),
        'method'  => 'PUT',
        'usuario' => $usuario,
        'roles'   => $roles,
    ])
</div>

@endsection
