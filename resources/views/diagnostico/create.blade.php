@extends('layouts.app')
@section('title', 'Diagnóstico Técnico')

@section('content')
<div style="max-width:860px;">
    <div style="margin-bottom:1rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
        <div>
            <a href="{{ route('autos.show', $orden->placa_auto) }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">← Volver a vehículo</a>
            <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; margin-top:.5rem;">Diagnóstico Técnico</h2>
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
                <div><strong>Estado:</strong> {{ $orden->estado }}</div>
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

        <div class="field-group" style="grid-column:1 / -1;">
            <label>Fallas Encontradas <span class="req">*</span></label>
            <div id="fallas-container" style="display:grid; gap:1rem;">
                @php
                    $oldFallas = old('fallas', ['']);
                @endphp
                @foreach($oldFallas as $index => $falla)
                <div class="field-group" style="position:relative;">
                    <textarea name="fallas[]" rows="2" placeholder="Describa la falla o hallazgo">{{ $falla }}</textarea>
                    @if($index > 0)
                        <button type="button" class="btn btn-ghost btn-sm remove-falla" style="position:absolute; top:0.5rem; right:0;">Eliminar</button>
                    @endif
                </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-ghost btn-sm" id="add-falla">+ Agregar falla</button>
        </div>

        <div class="field-group" style="grid-column:1 / -1;">
            <label for="descripcion"> Descripción <span class="req">*</span></label>
            <textarea id="descripcion" name="descripcion" rows="4" placeholder="Escriba la descripción del diagnóstico">{{ old('descripcion') }}</textarea>
        </div>

        <div class="field-group" style="grid-column:1 / -1;">
            <label for="resultado_general">Resultado General <span class="req">*</span></label>
            <textarea id="resultado_general" name="resultado_general" rows="4" placeholder="Escriba el dictamen final del mecánico">{{ old('resultado_general') }}</textarea>
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

    function createFallaField(value = '') {
        const wrapper = document.createElement('div');
        wrapper.className = 'field-group';
        wrapper.style.position = 'relative';

        const textarea = document.createElement('textarea');
        textarea.name = 'fallas[]';
        textarea.rows = 2;
        textarea.placeholder = 'Describa la falla o hallazgo';
        textarea.textContent = value;
        textarea.style.minHeight = '4rem';
        wrapper.appendChild(textarea);

        const remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'btn btn-ghost btn-sm remove-falla';
        remove.style.position = 'absolute';
        remove.style.top = '0.5rem';
        remove.style.right = '0';
        remove.textContent = 'Eliminar';
        remove.addEventListener('click', () => wrapper.remove());
        wrapper.appendChild(remove);

        return wrapper;
    }

    addButton.addEventListener('click', function () {
        container.appendChild(createFallaField(''));
    });

    container.addEventListener('click', function (event) {
        if (event.target.matches('.remove-falla')) {
            event.target.closest('.field-group').remove();
        }
    });
</script>
@endpush
