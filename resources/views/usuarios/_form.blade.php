{{-- resources/views/usuarios/_form.blade.php --}}
{{-- Variables: $action, $method, $roles, $usuario (null en create) --}}

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

        {{-- ── Datos de Persona ── --}}
        <div style="margin-bottom:1.5rem;">
            <div style="font-family:'Barlow Condensed',sans-serif; font-size:.85rem; font-weight:700;
                        letter-spacing:.1em; text-transform:uppercase; color:var(--accent);
                        margin-bottom:1rem; padding-bottom:.5rem; border-bottom:1px solid var(--border);">
                Datos personales
            </div>

            <div class="form-grid">

                {{-- CI con autocompletado --}}
                <div class="field-group {{ $errors->has('ci') ? 'has-error' : '' }}">
                    <label>CI / Documento <span class="req">*</span>
                        @if($usuario) <span class="hint">(no editable)</span> @endif
                    </label>
                    <div style="position:relative;">
                        <input type="text"
                               id="ci-input"
                               name="ci"
                               value="{{ old('ci', $usuario?->ci_personal ?? '') }}"
                               placeholder="Ej. 8512347"
                               {{ $usuario ? 'readonly' : 'required' }}
                               @if(!$usuario) oninput="buscarPersonaPorCI(this.value)" @endif
                               autocomplete="off">
                        <span id="ci-status" style="position:absolute; right:.75rem; top:50%;
                              transform:translateY(-50%); font-size:.72rem; color:var(--muted); display:none;">
                            buscando...
                        </span>
                    </div>
                    @error('ci') <span class="field-error">{{ $message }}</span> @enderror

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
                           value="{{ old('nombre', $usuario?->persona?->nombre ?? '') }}"
                           placeholder="Ej. Carlos Mamani" required>
                    @error('nombre') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="field-group">
                    <label>Teléfono</label>
                    <input type="text" id="telefono-input" name="telefono"
                           value="{{ old('telefono', $usuario?->persona?->telefono ?? '') }}"
                           placeholder="Ej. 72345678">
                </div>

                <div class="field-group">
                    <label>Dirección</label>
                    <input type="text" id="direccion-input" name="direccion"
                           value="{{ old('direccion', $usuario?->persona?->direccion ?? '') }}"
                           placeholder="Ej. Av. Banzer 4to Anillo">
                </div>

            </div>
        </div>

        {{-- ── Credenciales de acceso ── --}}
        <div>
            <div style="font-family:'Barlow Condensed',sans-serif; font-size:.85rem; font-weight:700;
                        letter-spacing:.1em; text-transform:uppercase; color:var(--accent);
                        margin-bottom:1rem; padding-bottom:.5rem; border-bottom:1px solid var(--border);">
                Credenciales de acceso
            </div>

            <div class="form-grid">

                <div class="field-group {{ $errors->has('id_rol') ? 'has-error' : '' }}">
                    <label>Rol <span class="req">*</span></label>
                    <select name="id_rol" required>
                        <option value="" disabled {{ !old('id_rol', $usuario?->id_rol) ? 'selected' : '' }}>
                            Seleccionar rol...
                        </option>
                        @foreach($roles as $rol)
                            <option value="{{ $rol->id }}"
                                {{ old('id_rol', $usuario?->id_rol) == $rol->id ? 'selected' : '' }}>
                                {{ $rol->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_rol') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="field-group {{ $errors->has('correo') ? 'has-error' : '' }}">
                    <label>Correo electrónico</label>
                    <input type="email" name="correo"
                           value="{{ old('correo', $usuario?->correo ?? '') }}"
                           placeholder="correo@taller.com">
                    @error('correo') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="field-group {{ $errors->has('clave') ? 'has-error' : '' }}">
                    <label>
                        Contraseña (Mayus. + minúsc. + números + símbolo)
                        @if(!$usuario) <span class="req">*</span>
                        @else <span class="hint">(vacío = no cambiar)</span>
                        @endif
                    </label>
                    <input type="password" name="clave"
                           placeholder="Mínimo 8 caracteres"
                           {{ !$usuario ? 'required' : '' }}>
                    @error('clave') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="field-group">
                    <label>Confirmar contraseña
                        @if(!$usuario) <span class="req">*</span> @endif
                    </label>
                    <input type="password" name="clave_confirmation"
                           placeholder="Repetir contraseña"
                           {{ !$usuario ? 'required' : '' }}>
                </div>

            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('usuarios.index') }}" class="btn btn-ghost">← Cancelar</a>
            <button type="submit" class="btn btn-primary">
                {{ $usuario ? '💾 Guardar cambios' : '＋ Crear usuario' }}
            </button>
        </div>

    </div>
</form>

@if(!$usuario)
@push('scripts')
<script>
let _ciTimer = null;

function buscarPersonaPorCI(ci) {
    clearTimeout(_ciTimer);
    const status = document.getElementById('ci-status');
    const banner = document.getElementById('persona-encontrada');
    const flags  = document.getElementById('persona-flags');

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
                document.getElementById('nombre-input').value    = data.nombre    || '';
                document.getElementById('telefono-input').value  = data.telefono  || '';
                document.getElementById('direccion-input').value = data.direccion || '';

                let flagTexto = '';
                if (data.es_cliente && data.es_personal) flagTexto = '(ya es cliente y personal del taller)';
                else if (data.es_cliente)  flagTexto = '(registrado como cliente — se agregará como personal)';
                else if (data.es_personal) flagTexto = '(ya es personal del taller)';

                flags.textContent    = ' ' + flagTexto;
                banner.style.display = 'block';
            } else {
                banner.style.display = 'none';
            }
        } catch (e) {
            status.style.display = 'none';
        }
    }, 500);
}
</script>
@endpush
@endif