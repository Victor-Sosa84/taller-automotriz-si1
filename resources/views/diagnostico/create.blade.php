@extends('layouts.app')
@section('title', 'Diagnóstico Técnico')

@section('content')
<div style="max-width:860px;">
    <div style="margin-bottom:1rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
        <div>
            @if(isset($from) && $from === 'auto')
                <a href="{{ route('autos.show', $orden->placa_auto) }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">← Volver a vehículo</a>
            @elseif(isset($from) && $from === 'historial')
                <a href="{{ route('historial.show', $orden->placa_auto) }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">← Volver a historial</a>
            @endif
            <div style="display:flex; align-items:center; gap:.75rem; margin-top:.5rem; flex-wrap:wrap;">
                <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; margin:0;">Diagnóstico Técnico</h2>
                <span style="background:var(--accent); color:#000; font-size:.75rem; font-weight:700; padding:.25rem .75rem; border-radius:999px; text-transform:uppercase; letter-spacing:.05em;">
                    {{ $orden->estado }}
                </span>
            </div>
        </div>
        <div style="text-align:right; min-width:220px;">
            <div style="font-size:.75rem; color:var(--muted); text-transform:uppercase; letter-spacing:.08em; margin-bottom:.4rem;">Placa</div>
            <div style="font-size:1.5rem; font-weight:800; color:var(--accent);">{{ $orden->placa_auto }}</div>
            <div style="margin-top:.4rem; color:var(--muted);">Orden de trabajo #{{ $orden->nro }}</div>
        </div>
    </div>

    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header">
            <span class="card-title">Datos de ingreso</span>
        </div>
        <div class="card-body">
            <div style="display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap:1rem;">
                <div><strong>Kilometraje:</strong> {{ number_format($orden->kilometraje ?? 0) }}</div>
                <div style="grid-column:1 / -1;"><strong>Detalles de ingreso:</strong> {{ $orden->observacion_entrada }}</div>
            </div>
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

    <form action="{{ route('diagnostico.store') }}" method="POST" id="diagnostico-form" class="form-card">
        @csrf
        <input type="hidden" name="orden_id" value="{{ $orden->nro }}" />

        <div class="field-group" style="grid-column:1 / -1; margin-bottom:.5rem;">
            <label>Fallas Encontradas <span class="req">*</span></label>
            <div id="fallas-container" style="display:flex; flex-direction:column; gap:.75rem;">
                @php $oldFallas = old('fallas', ['']); @endphp
                @foreach($oldFallas as $index => $falla)
                    <div style="display:flex; align-items:flex-start; gap:.5rem;">
                        <textarea name="fallas[]" rows="2" placeholder="Describa la falla o hallazgo"
                            style="flex:1; resize:vertical; box-sizing:border-box;">{{ $falla }}</textarea>
                        @if($index > 0)
                            <button type="button" class="btn btn-ghost btn-sm remove-falla"
                                style="flex-shrink:0;">Eliminar</button>
                        @endif
                    </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-ghost btn-sm" id="add-falla" 
                style="margin-top:.75rem; width:fit-content;">
                + Agregar falla
            </button>
        </div>

        {{-- separador visual --}}
        <div style="grid-column:1 / -1; border-top:1px solid var(--border); margin:.5rem 0;"></div>

        <div class="field-group" style="grid-column:1 / -1;">
            <label for="descripcion">Descripción del Diagnóstico <span class="req">*</span></label>
            <textarea id="descripcion" name="descripcion" rows="4"
                style="width:100%; box-sizing:border-box; resize:vertical;"
                placeholder="Describe los hallazgos y el dictamen técnico...">{{ old('descripcion') }}</textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Finalizar Diagnóstico</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const container = document.getElementById('fallas-container');
    const addButton = document.getElementById('add-falla');
    const MAX_FALLAS = 5;

    function updateAddButton() {
        const count = container.querySelectorAll('textarea[name="fallas[]"]').length;
        addButton.disabled = count >= MAX_FALLAS;
        addButton.textContent = count >= MAX_FALLAS ? `Máximo ${MAX_FALLAS} fallas` : '+ Agregar falla';
    }

    function createFallaField(value = '') {
        const wrapper = document.createElement('div');
        wrapper.style.cssText = 'display:flex; align-items:center; gap:.5rem;';

        const textarea = document.createElement('textarea');
        textarea.name = 'fallas[]';
        textarea.rows = 2;
        textarea.placeholder = 'Describa la falla o hallazgo';
        textarea.textContent = value;
        textarea.style.cssText = 'flex:1; resize:vertical;';
        wrapper.appendChild(textarea);

        const remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'btn btn-ghost btn-sm remove-falla';
        remove.textContent = 'Eliminar';
        remove.style.cssText = 'flex-shrink:0; align-self:flex-start;';
        remove.addEventListener('click', () => { wrapper.remove(); updateAddButton(); });
        wrapper.appendChild(remove);

        return wrapper;
    }

    addButton.addEventListener('click', function () {
        if (container.querySelectorAll('textarea[name="fallas[]"]').length < MAX_FALLAS) {
            container.appendChild(createFallaField(''));
            updateAddButton();
        }
    });
</script>
@endpush
