{{-- resources/views/clientes/_form.blade.php --}}
{{-- Variables requeridas: $action, $method, $cliente (null en create) --}}

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
            Datos del cliente
        </div>

        <div class="form-grid">

            {{-- CI --}}
            <div class="field-group {{ $errors->has('ci') ? 'has-error' : '' }}">
                <label>CI / Documento <span class="req">*</span>
                    @if($cliente) <span class="hint">(no editable)</span> @endif
                </label>
                <input type="text" name="ci"
                       value="{{ old('ci', $cliente?->ci ?? '') }}"
                       placeholder="Ej. 8512347"
                       {{ $cliente ? 'readonly' : 'required' }}>
                @error('ci') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            {{-- Nombre --}}
            <div class="field-group {{ $errors->has('nombre') ? 'has-error' : '' }}">
                <label>Nombre completo <span class="req">*</span></label>
                <input type="text" name="nombre"
                       value="{{ old('nombre', $cliente?->nombre ?? '') }}"
                       placeholder="Ej. Juan Pérez" required>
                @error('nombre') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            {{-- Teléfono --}}
            <div class="field-group">
                <label>Teléfono</label>
                <input type="text" name="telefono"
                       value="{{ old('telefono', $cliente?->telefono ?? '') }}"
                       placeholder="Ej. 72345678">
            </div>

            {{-- Dirección --}}
            <div class="field-group">
                <label>Dirección</label>
                <input type="text" name="direccion"
                       value="{{ old('direccion', $cliente?->direccion ?? '') }}"
                       placeholder="Ej. Av. Principal 123">
            </div>

        </div>

        <div class="form-actions">
            <a href="{{ route('clientes.index') }}" class="btn btn-ghost">← Cancelar</a>
            <button type="submit" class="btn btn-primary">
                {{ $cliente ? '💾 Guardar cambios' : '＋ Registrar cliente' }}
            </button>
        </div>

    </div>
</form>
