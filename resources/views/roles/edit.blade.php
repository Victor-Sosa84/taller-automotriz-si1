@extends('layouts.app')
@section('title', 'Editar Perfil')
@section('content')
<div style="max-width:600px;">
    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('roles.index') }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">← Volver a perfiles</a>
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; text-transform:uppercase; margin-top:.5rem;">Editar — {{ $role->nombre }}</h2>
    </div>
    @if($errors->any())
        <div class="form-errors"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    <form action="{{ route('roles.update', $role) }}" method="POST">
        @csrf @method('PUT')
        <div class="form-card">
            <div class="form-grid">
                <div class="field-group {{ $errors->has('nombre') ? 'has-error' : '' }}" style="grid-column:1/-1;">
                    <label>Nombre del perfil <span class="req">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $role->nombre) }}" required>
                    @error('nombre') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="field-group" style="grid-column:1/-1;">
                    <label>Descripción</label>
                    <input type="text" name="descripcion" value="{{ old('descripcion', $role->descripcion) }}">
                </div>
            </div>
            <div class="form-actions">
                <a href="{{ route('roles.index') }}" class="btn btn-ghost">← Cancelar</a>
                <button type="submit" class="btn btn-primary">💾 Guardar cambios</button>
            </div>
        </div>
    </form>
</div>
@endsection