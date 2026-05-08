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
                <input type="text"
                        id="placa-input"
                        name="placa"
                        value="{{ old('placa', $auto?->placa ?? '') }}"
                        placeholder="Ej. 3046FIJ"
                        maxlength="9"
                        style="text-transform:uppercase; letter-spacing:.05em;"
                        {{ $auto ? 'readonly' : 'required' }}
                        @if(!$auto)
                            oninput="formatearPlaca(this)"
                        @endif
                        autocomplete="off">
                <span style="font-size:.7rem; color:var(--muted); margin-top:.2rem;">
                    Formato Bolivia: 3-4 dígitos seguidos de 2-3 letras (ej. 3046FIJ, 173YYY)
                </span>
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
                <input type="text"
                        name="anio"
                        id="anio-input"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        maxlength="4"
                        value="{{ old('anio', $auto?->anio ?? '') }}"
                        placeholder="Ej. 2018"
                        oninput="this.value = this.value.replace(/\D/g, '').slice(0, 4)">
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

@if(!$auto)
@push('scripts')
<script>
function formatearPlaca(input) {
    // Convertir a mayúsculas y remover caracteres no válidos
    let val = input.value.toUpperCase().replace(/[^A-Z0-9\-]/g, '');

    // Limitar a 9 caracteres máximo
    val = val.slice(0, 9);
    input.value = val;

    // Validación visual en tiempo real
    const hint = input.parentElement.querySelector('span');
    const esValida = /^\d{3,4}[A-Z]{2,3}$/.test(val);

    if (val.length >= 5) {
        if (esValida) {
            input.style.borderColor = 'var(--success)';
        } else {
            input.style.borderColor = 'var(--danger)';
        }
    } else {
        input.style.borderColor = '';
    }
}
</script>
@endpush
@endif