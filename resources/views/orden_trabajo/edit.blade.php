@extends('layouts.app')
@section('title', 'Editar Orden #' . $orden->nro)

@section('content')
<div style="max-width:760px;">
    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('orden_trabajo.show', $orden->nro) }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">← Volver al detalle</a>
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; margin-top:.5rem;">
            Editar Orden #{{ $orden->nro }}
        </h2>
        <p style="color:var(--muted); font-size:.95rem; margin-top:.25rem;">Modifique el estado u observaciones de la orden de trabajo.</p>
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

    {{-- Info de solo lectura --}}
    <div class="form-card" style="margin-bottom:1rem;">
        <div class="form-grid">
            <div class="field-group">
                <label>Placa</label>
                <p style="margin:0; font-weight:600;">{{ $orden->auto->placa ?? '—' }}</p>
            </div>
            <div class="field-group">
                <label>Proforma</label>
                <p style="margin:0;">#{{ $orden->nro_proforma ?? '—' }}</p>
            </div>
            <div class="field-group">
                <label>Cliente</label>
                <p style="margin:0;">{{ $orden->proforma->cliente->nombre ?? '—' }}</p>
            </div>
            <div class="field-group">
                <label>Total Aprox.</label>
                <p style="margin:0;">{{ $orden->proforma ? 'Bs. ' . number_format($orden->proforma->total_aprox, 2) : '—' }}</p>
            </div>
        </div>
    </div>

    <form action="{{ route('orden_trabajo.update', $orden->nro) }}" method="POST" class="form-card">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="field-group">
                <label for="estado">Estado <span class="req">*</span></label>
                @php
                $estados = match($orden->estado) {
                    'Pendiente de Diagnóstico' => ['Pendiente de Diagnóstico', 'En Proceso', 'Anulada'],
                    'Diagnóstico Finalizado'   => ['Diagnóstico Finalizado', 'En Proceso', 'Anulada'],
                    'En Proceso'               => ['En Proceso', 'Finalizada', 'Anulada'],
                    'Finalizada'               => ['Finalizada'],
                    'Anulada'                  => ['Anulada'],
                    default                    => [$orden->estado],
                };
                @endphp
                <select id="estado" name="estado">
                    @foreach($estados as $estado)
                        <option value="{{ $estado }}" {{ old('estado', $orden->estado) === $estado ? 'selected' : '' }}>
                            {{ $estado }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field-group">
                <label for="fecha_fin">Fecha de Finalización</label>
                <input id="fecha_fin" name="fecha_fin" type="date"
                    value="{{ old('fecha_fin', $orden->fecha_fin?->format('Y-m-d')) }}" />
            </div>

            <div class="field-group" style="grid-column:1 / -1;">
                <label for="observacion_salida">Observación de Salida</label>
                <textarea id="observacion_salida" name="observacion_salida" rows="4"
                    style="width:100%; box-sizing:border-box; resize:vertical;"
                    placeholder="Detalles del trabajo realizado, condiciones de entrega...">{{ old('observacion_salida', $orden->observacion_salida) }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('orden_trabajo.show', $orden->nro) }}" class="btn btn-ghost" style="color:var(--muted);">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </form>
</div>
@endsection