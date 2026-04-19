{{-- resources/views/usuarios/_form.blade.php --}}
{{-- Variables requeridas: $action, $method, $roles, $usuario (null en create) --}}

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

        {{-- ── SECCIÓN: Datos de Persona ── --}}
        <div style="margin-bottom:1.5rem;">
            <div style="font-family:'Barlow Condensed',sans-serif; font-size:.85rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--accent); margin-bottom:1rem; padding-bottom:.5rem; border-bottom:1px solid var(--border);">
                Datos personales
            </div>

            <div class="form-grid">

                <div class="field-group {{ $errors->has('ci') ? 'has-error' : '' }}">
                    <label>CI / Documento <span class="req">*</span>
                        @if($usuario) <span class="hint">(no editable)</span> @endif
                    </label>
                    <input type="text" name="ci"
                           value="{{ old('ci', $usuario?->ci_personal ?? '') }}"
                           placeholder="Ej. 8512347"
                           {{ $usuario ? 'readonly' : 'required' }}>
                    @error('ci') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="field-group {{ $errors->has('nombre') ? 'has-error' : '' }}">
                    <label>Nombre completo <span class="req">*</span></label>
                    <input type="text" name="nombre"
                           value="{{ old('nombre', $usuario?->persona?->nombre ?? '') }}"
                           placeholder="Ej. Carlos Mamani" required>
                    @error('nombre') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="field-group">
                    <label>Teléfono</label>
                    <input type="text" name="telefono"
                           value="{{ old('telefono', $usuario?->persona?->telefono ?? '') }}"
                           placeholder="Ej. 72345678">
                </div>

                <div class="field-group">
                    <label>Dirección</label>
                    <input type="text" name="direccion"
                           value="{{ old('direccion', $usuario?->persona?->direccion ?? '') }}"
                           placeholder="Ej. Av. Banzer 4to Anillo">
                </div>

            </div>
        </div>

        {{-- ── SECCIÓN: Datos de Acceso ── --}}
        <div>
            <div style="font-family:'Barlow Condensed',sans-serif; font-size:.85rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--accent); margin-bottom:1rem; padding-bottom:.5rem; border-bottom:1px solid var(--border);">
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
                        Contraseña (Mayus. + minúsc. + números)
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
