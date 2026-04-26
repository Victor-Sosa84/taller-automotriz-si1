{{-- resources/views/autos/_form.blade.php --}}
{{-- Variables: $action, $method, $auto (null en create) --}}

@if($errors->any())
    <div class="form-errors">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ $action }}" method="POST" novalidate>
    @csrf
    @if($method !== 'POST') @method($method) @endif

    <div class="form-card">

        <div style="font-family:'Barlow Condensed',sans-serif; font-size:.85rem; font-weight:700;
                    letter-spacing:.1em; text-transform:uppercase; color:var(--accent);
                    margin-bottom:1rem; padding-bottom:.5rem; border-bottom:1px solid var(--border);">
            Ficha técnica del vehículo
        </div>

        <div class="form-grid">

            {{-- Placa --}}
            <div class="field-group {{ $errors->has('placa') ? 'has-error' : '' }}">
                <label>Placa <span class="req">*</span>
                    @if($auto) <span class="hint">(no editable)</span> @endif
                </label>
                <input type="text" name="placa"
                       value="{{ old('placa', $auto?->placa ?? '') }}"
                       placeholder="Ej. ABC-1234"
                       style="text-transform:uppercase;"
                       {{ $auto ? 'readonly' : 'required' }}>
                @error('placa') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            {{-- Marca --}}
            <div class="field-group {{ $errors->has('marca') ? 'has-error' : '' }}">
                <label>Marca</label>
                <input type="text" name="marca"
                       value="{{ old('marca', $auto?->marca ?? '') }}"
                       placeholder="Ej. Toyota, Nissan, Ford">
                @error('marca') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            {{-- Modelo --}}
            <div class="field-group {{ $errors->has('modelo') ? 'has-error' : '' }}">
                <label>Modelo</label>
                <input type="text" name="modelo"
                       value="{{ old('modelo', $auto?->modelo ?? '') }}"
                       placeholder="Ej. Corolla, Sentra, F-150">
                @error('modelo') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            {{-- Año --}}
            <div class="field-group {{ $errors->has('anio') ? 'has-error' : '' }}">
                <label>Año</label>
                <input type="number" name="anio"
                       value="{{ old('anio', $auto?->anio ?? '') }}"
                       placeholder="Ej. 2018"
                       min="1900" max="{{ date('Y') + 1 }}">
                @error('anio') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            {{-- Color --}}
            <div class="field-group">
                <label>Color</label>
                <input type="text" name="color"
                       value="{{ old('color', $auto?->color ?? '') }}"
                       placeholder="Ej. Blanco perla">
            </div>

        </div>

        <div class="form-actions">
            <a href="{{ route('autos.index') }}" class="btn btn-ghost">← Cancelar</a>
            <button type="submit" class="btn btn-primary">
                {{ $auto ? '💾 Guardar cambios' : '＋ Registrar vehículo' }}
            </button>
        </div>

    </div>
</form>
