{{-- resources/views/clientes/_form.blade.php --}}
{{-- Variables: $action, $method, $cliente (null en create) --}}

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

            {{-- CI con autocompletado --}}
            <div class="field-group {{ $errors->has('ci') ? 'has-error' : '' }}">
                <label>CI / Documento <span class="req">*</span>
                    @if($cliente) <span class="hint">(no editable)</span> @endif
                </label>
                <div style="position:relative;">
                    <input type="text"
                           id="ci-input"
                           name="ci"
                           value="{{ old('ci', $cliente?->ci ?? '') }}"
                           placeholder="Ej. 8512347"
                           {{ $cliente ? 'readonly' : 'required' }}
                           @if(!$cliente) oninput="buscarPersonaPorCI(this.value)" @endif
                           autocomplete="off">
                    {{-- Indicador de búsqueda --}}
                    <span id="ci-status" style="position:absolute; right:.75rem; top:50%;
                          transform:translateY(-50%); font-size:.72rem; color:var(--muted); display:none;">
                        buscando...
                    </span>
                </div>
                @error('ci') <span class="field-error">{{ $message }}</span> @enderror

                {{-- Banner cuando se encuentra persona existente --}}
                <div id="persona-encontrada" style="display:none; margin-top:.4rem;
                     background:rgba(245,166,35,.08); border:1px solid rgba(245,166,35,.25);
                     border-radius:4px; padding:.5rem .75rem; font-size:.78rem; color:var(--accent);">
                    ⚡ Persona encontrada — datos autocargados.
                    <span id="persona-flags" style="color:var(--muted);"></span>
                </div>
            </div>

            {{-- Nombre --}}
            <div class="field-group {{ $errors->has('nombre') ? 'has-error' : '' }}">
                <label>Nombre completo <span class="req">*</span></label>
                <input type="text" id="nombre-input" name="nombre"
                       value="{{ old('nombre', $cliente?->nombre ?? '') }}"
                       placeholder="Ej. Juan Pérez" required>
                @error('nombre') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            {{-- Teléfono --}}
            <div class="field-group">
                <label>Teléfono</label>
                <input type="text" id="telefono-input" name="telefono"
                       value="{{ old('telefono', $cliente?->telefono ?? '') }}"
                       placeholder="Ej. 72345678">
            </div>

            {{-- Dirección --}}
            <div class="field-group">
                <label>Dirección</label>
                <input type="text" id="direccion-input" name="direccion"
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

@if(!$cliente)
@push('scripts')
<script>
let _ciTimer = null;

function buscarPersonaPorCI(ci) {
    clearTimeout(_ciTimer);
    const status   = document.getElementById('ci-status');
    const banner   = document.getElementById('persona-encontrada');
    const flags    = document.getElementById('persona-flags');

    if (ci.length < 4) {
        banner.style.display = 'none';
        status.style.display = 'none';
        return;
    }

    status.style.display = 'inline';
    status.textContent   = 'buscando...';

    _ciTimer = setTimeout(async () => {
        try {
            const res  = await fetch('/api/persona/' + encodeURIComponent(ci), {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            status.style.display = 'none';

            if (data) {
                // Autocompletar campos
                document.getElementById('nombre-input').value    = data.nombre    || '';
                document.getElementById('telefono-input').value  = data.telefono  || '';
                document.getElementById('direccion-input').value = data.direccion || '';

                // Mostrar banner con info de flags
                let flagTexto = '';
                if (data.es_cliente && data.es_personal) flagTexto = '(ya es cliente y personal)';
                else if (data.es_cliente)  flagTexto = '(ya registrado como cliente)';
                else if (data.es_personal) flagTexto = '(ya registrado como personal)';

                flags.textContent    = ' ' + flagTexto;
                banner.style.display = 'block';
            } else {
                banner.style.display = 'none';
            }
        } catch (e) {
            status.style.display = 'none';
        }
    }, 500); // espera 500ms después de que el usuario deja de escribir
}
</script>
@endpush
@endif