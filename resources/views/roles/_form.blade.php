{{-- resources/views/roles/_form.blade.php --}}
@if($errors->any())
    <div class="form-errors"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif
<form action="{{ $action }}" method="POST" novalidate>
    @csrf
    @if($method !== 'POST') @method($method) @endif
    <div class="form-card">
        <div class="form-grid">
            <div class="field-group {{ $errors->has('nombre') ? 'has-error' : '' }}" style="grid-column:1/-1;">
                <label>Nombre del perfil <span class="req">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $role?->nombre ?? '') }}"
                       placeholder="Ej. Recepcionista, Mecánico Senior" required>
                @error('nombre') <span class="field-error">{{ $message }}</span> @enderror
            </div>
            <div class="field-group" style="grid-column:1/-1;">
                <label>Descripción</label>
                <input type="text" name="descripcion" value="{{ old('descripcion', $role?->descripcion ?? '') }}"
                       placeholder="Breve descripción del perfil">
            </div>
        </div>
        <div class="form-actions">
            <a href="{{ route('roles.index') }}" class="btn btn-ghost">← Cancelar</a>
            <button type="submit" class="btn btn-primary">
                {{ $role ? '💾 Guardar cambios' : '＋ Crear perfil' }}
            </button>
        </div>
    </div>
</form>
