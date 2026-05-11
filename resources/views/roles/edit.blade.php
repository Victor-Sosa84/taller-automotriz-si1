{{-- resources/views/roles/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Editar Perfil')
@section('content')
<div style="max-width:600px;">
    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('roles.index') }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">← Volver a perfiles</a>
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; text-transform:uppercase; margin-top:.5rem;">
            Editar — {{ $role->nombre }}
        </h2>
    </div>
    @include('roles._form', ['action' => route('roles.update', $role), 'method' => 'PUT', 'role' => $role])
</div>
@endsection
