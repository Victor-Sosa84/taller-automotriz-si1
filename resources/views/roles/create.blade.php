{{-- resources/views/roles/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Nuevo Perfil')
@section('content')
<div style="max-width:600px;">
    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('roles.index') }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">← Volver a perfiles</a>
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; text-transform:uppercase; margin-top:.5rem;">Nuevo Perfil</h2>
        <p style="color:var(--muted); font-size:.85rem; margin-top:.2rem;">Después de crear el perfil, asigna sus privilegios desde la sección Permisos.</p>
    </div>
    @include('roles._form', ['action' => route('roles.store'), 'method' => 'POST', 'role' => null])
</div>
@endsection
