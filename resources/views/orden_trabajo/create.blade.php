@extends('layouts.app')
@section('title', 'Registro de Unidad')

@section('content')
<div style="max-width:760px;">
    <div style="margin-bottom:1.5rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
        <div>
            <a href="{{ route('autos.index') }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">← Volver a vehículos</a>
            <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; margin-top:.5rem;">Registro de Unidad</h2>
            <p style="color:var(--muted); font-size:.95rem; margin-top:.25rem;">Complete los datos de ingreso para continuar con el diagnóstico.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="form-errors">
            <strong>Por favor corrige los siguientes campos:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('orden-trabajo.store') }}" method="POST" class="form-card">
        @csrf

        <div class="form-grid">
            <div class="field-group">
                <label for="placa">Placa <span class="req">*</span></label>
                <input id="placa" name="placa" type="text" 
                    value="{{ old('placa', $placa ?? $auto?->placa) }}" 
                    {{ $auto ? 'readonly' : '' }} 
                    placeholder="Ej. 3046FIJ"
                    maxlength="9"
                    autocomplete="off"
                    style="text-transform:uppercase; letter-spacing:.05em;"
                    @if(!$auto) oninput="formatearPlaca(this)" @endif />
                @if(!$auto)
                    <span style="font-size:.7rem; color:var(--muted); margin-top:.2rem;">
                        Formato Bolivia: 3-4 dígitos seguidos de 2-3 letras (ej. 3046FIJ, 173YYY)
                    </span>
                @endif
            </div>
            <div class="field-group">
                <label for="kilometraje">Kilometraje <span class="req">*</span></label>
                <input id="kilometraje" name="kilometraje" type="number" min="0" max="999999"
                    value="{{ old('kilometraje') }}" 
                    placeholder="Ej. 125000"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);"
                    style="width:100%; box-sizing:border-box;" />
            </div>

            <div class="field-group">
                <label for="combustible">Combustible <span class="req">*</span></label>
                <select id="combustible" name="combustible">
                    <option value="">Seleccionar...</option>
                    @foreach(['Vacío','1/4','1/2','3/4','Lleno'] as $opcion)
                        <option value="{{ $opcion }}" {{ old('combustible') === $opcion ? 'selected' : '' }}>{{ $opcion }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field-group">
                <label>Inventario</label>
                <div style="display:grid; grid-template-columns:repeat(2, minmax(0,1fr)); gap:.5rem .75rem; margin-top:.25rem;">
                    @foreach(['Llanta Auxilio','Gato','Herramientas','Radio'] as $item)
                        <label style="display:flex; align-items:center; gap:.6rem; font-size:.9rem; color:var(--text); cursor:pointer; padding:.4rem .6rem; border-radius:.4rem; background:var(--surface-2, rgba(255,255,255,.05));">
                            <input type="checkbox" name="inventario[]" value="{{ $item }}"
                                {{ in_array($item, old('inventario', [])) ? 'checked' : '' }}
                                style="width:1rem; height:1rem; accent-color:var(--accent); cursor:pointer;">
                            {{ $item }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="field-group" style="grid-column:1 / -1;">
                <label for="observaciones_adicionales">Observaciones Adicionales</label>
                <textarea id="observaciones_adicionales" name="observaciones_adicionales" rows="4"
                    style="width:100%; box-sizing:border-box; resize:vertical;"
                    placeholder="Rayaduras, golpes, detalles físicos u otras observaciones...">{{ old('observaciones_adicionales') }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Registrar y Continuar al Diagnóstico</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function formatearPlaca(input) {
    const cursor = input.selectionStart;
    const anterior = input.value;
    
    let val = anterior.toUpperCase().replace(/[^A-Z0-9\-]/g, '').slice(0, 9);
    
    if (input.value !== val) {
        input.value = val;
        input.setSelectionRange(cursor, cursor);
    }

    const esValida = /^\d{3,4}[A-Z]{2,3}$/.test(val);
    if (val.length >= 5) {
        input.style.borderColor = esValida ? 'var(--success)' : 'var(--danger)';
    } else {
        input.style.borderColor = '';
    }
}
</script>
@endpush
