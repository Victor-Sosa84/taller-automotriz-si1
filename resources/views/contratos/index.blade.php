@extends('layouts.app')
@section('title', 'Contratos de Trabajo')

@section('content')

{{-- ESTILOS ADICIONALES PARA IMPRESIÓN Y DISEÑO --}}
<style>
    /* Estilo por defecto en la web: Ocultar la sección de firmas en el modal */
    .only-print {
        display: none !important;
    }

    /* Ocultar elementos innecesarios al mandar a la impresora/PDF */
    @media print {

        /* Ocultar entorno de la app y elementos interactivos del modal */
        .sidebar,
        .navbar,
        .btn,
        .table-wrap,
        .form-actions,
        h2,
        p {
            display: none !important;
        }

        /* Mostrar la sección de firmas únicamente en el PDF/Impresión */
        .only-print {
            display: flex !important;
        }

        /* Forzar que el modal e imprimible se visualicen correctamente en papel */
        #modalVerContrato {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            height: auto !important;
            background: white !important;
            display: block !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .modal-content-print {
            background: white !important;
            color: black !important;
            border: none !important;
            box-shadow: none !important;
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        /* Asegurar legibilidad del texto en el PDF impreso */
        #documento-imprimible * {
            color: black !important;
        }

        #det-id,
        #det-valor {
            color: #1a202c !important;
            /* Color oscuro sólido para resaltar valores en papel */
        }

        /* Forzar que la línea punteada de la firma sea visiblemente negra en papel */
        .linea-firma {
            border-top: 1px dashed #000000 !important;
        }
    }
</style>

<div
    style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
    <div>
        <h2
            style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; text-transform:uppercase;">
            Contratos de Trabajo
        </h2>
        <p style="color:var(--muted); font-size:.85rem; margin-top:.2rem;">
            Gestión contractual y condiciones salariales del personal de JECOES Tronic.
        </p>
    </div>
    <button class="btn btn-primary" onclick="abrirModalCrear()">＋ Nuevo Contrato</button>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Empleado (CI)</th>
                <th>Tipo Remuneración</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Período</th>
                <th>Monto / Valor</th>
                <th>Estado</th>
                <th style="text-align:center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($contratos as $contrato)
            <tr>
                <td class="td-muted" style="font-family:monospace;">#{{ $contrato->id }}</td>
                <td style="font-weight:600;">
                    {{ $contrato->personal->nombre }}
                    <span class="td-muted" style="font-size:.75rem; font-family:monospace; display:block;">CI: {{
                        $contrato->ci_personal }}</span>
                </td>
                <td>
                    <span class="badge {{ $contrato->tipo_remuneracion == 1 ? 'badge-recep' : 'badge-mec' }}">
                        {{ $contrato->modalidadRemuneracion->descripcion ?? ($contrato->tipo_remuneracion == 1 ? 'Sueldo
                        Fijo' : 'Porcentaje por Comisión') }}
                    </span>
                </td>
                <td class="td-muted">{{ \Carbon\Carbon::parse($contrato->fecha_inicio)->format('d/m/Y') }}</td>
                <td class="td-muted">{{ $contrato->fecha_fin ?
                    \Carbon\Carbon::parse($contrato->fecha_fin)->format('d/m/Y') : 'Indefinido' }}</td>
                <td>{{ $contrato->periodo_pago ?? '—' }}</td>
                <td style="font-weight:700; color:var(--accent);">{{ number_format($contrato->valor, 2) }} {{
                    $contrato->tipo_remuneracion == 1 ? 'Bs.' : '%' }}</td>
                <td>
                    @if($contrato->estado == 'Vigente')
                    <span class="badge badge-admin"
                        style="background:rgba(46,204,113,.15); color:var(--success); border-color:rgba(46,204,113,.3);">Vigente</span>
                    @else
                    <span class="badge badge-ghost"
                        style="background:rgba(255,255,255,.05); color:var(--muted); border:1px solid var(--border);">{{
                        $contrato->estado }}</span>
                    @endif
                </td>
                <td style="text-align:center;">
                    <div style="display:flex; gap:.4rem; justify-content:center;">
                        <button onclick="verContrato({{ $contrato->id }})" class="btn btn-sm btn-ghost"
                            title="Ver Detalle">👁</button>
                        @if($contrato->estado == 'Vigente')
                        <form method="POST" action="{{ route('contratos.baja', $contrato->id) }}"
                            style="display:inline;"
                            onsubmit="return confirm('¿Está seguro de dar de baja este contrato?')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger" title="Dar de baja">✕</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align:center; padding:2.5rem; color:var(--muted);">
                    No se encontraron contratos laborales registrados.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- MODAL PARA CREAR CONTRATO --}}
<div id="modalContrato"
    style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.75); z-index:1000; align-items:center; justify-content:center; padding:1rem;">
    <div class="form-card" style="width:100%; max-width:600px; max-height:90vh; overflow-y:auto;">
        <h3
            style="font-family:'Barlow Condensed',sans-serif; font-size:1.4rem; font-weight:700; text-transform:uppercase; margin-bottom:1.5rem; color:var(--accent);">
            Registrar Nuevo Contrato
        </h3>

        <form action="{{ route('contratos.store') }}" method="POST">
            @csrf
            <div class="form-grid">
                <div class="field-group">
                    <label>Personal Seleccionado <span class="req">*</span></label>
                    <select name="ci_personal" required>
                        <option value="">-- Seleccione un Empleado --</option>
                        @foreach($personalDisponible as $per)
                        <option value="{{ $per->ci }}">{{ $per->nombre }} (CI: {{ $per->ci }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="field-group">
                    <label>Tipo Remuneración <span class="req">*</span></label>
                    <select name="tipo_remuneracion" id="tipo_remuneracion" required>
                        <option value="">-- Seleccione Tipo --</option>
                        <option value="1">Sueldo Fijo</option>
                        <option value="2">Porcentaje por Comisión</option>
                    </select>
                </div>

                <div class="field-group">
                    <label>Fecha Inicio <span class="req">*</span></label>
                    <input type="date" name="fecha_inicio" required value="{{ date('Y-m-d') }}">
                </div>

                <div class="field-group">
                    <label>Fecha Fin <span class="hint">(Opcional)</span></label>
                    <input type="date" name="fecha_fin">
                </div>

                <div class="field-group">
                    <label>Periodo de Pago <span class="req">*</span></label>
                    <select name="periodo_pago" required>
                        <option value="Mensual">Mensual</option>
                        <option value="Quincenal">Quincenal</option>
                        <option value="Semanal" selected>Semanal</option>
                    </select>
                </div>

                <div class="field-group">
                    <label id="labelValor">Monto Base / Porcentaje <span class="req">*</span></label>
                    <input type="number" name="valor" step="0.01" min="0" required placeholder="Ej: 2500 o 15%">
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-ghost" onclick="cerrarModalCrear()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Contrato</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL DETALLE DEL CONTRATO --}}
<div id="modalVerContrato"
    style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.8); z-index:1100; align-items:center; justify-content:center; padding:1rem;">
    <div class="form-card modal-content-print"
        style="width:100%; max-width:650px; background:#1e293b; border:1px solid var(--border); padding:2rem; border-radius:8px;">

        <div id="documento-imprimible">
            {{-- Encabezado --}}
            <div
                style="text-align:center; margin-bottom:2rem; border-bottom:2px solid var(--accent); padding-bottom:1rem;">
                <h2
                    style="font-family:'Barlow Condensed',sans-serif; font-size:1.6rem; font-weight:800; text-transform:uppercase; color:white; margin:0;">
                    JECOES TRONIC
                </h2>
                <p style="font-size:.8rem; color:var(--muted); margin:4px 0 0 0;">DETALLES GENERALES DEL CONTRATO
                    LABORAL</p>
            </div>

            {{-- Cuerpo de datos --}}
            <div
                style="display:grid; grid-template-columns:1fr 1fr; gap:1.2rem; margin-bottom:1rem; color:white; font-size:.95rem;">
                <div><strong>Nro. Contrato:</strong> <span id="det-id"
                        style="font-family:monospace; color:var(--accent);"></span></div>
                <div><strong>Estado Actual:</strong> <span id="det-estado" style="font-weight:700;"></span></div>
                <div style="grid-column: span 2; border-top:1px solid rgba(255,255,255,.1); padding-top:.5rem;">
                    <strong>Empleado:</strong> <span id="det-nombre"></span>
                </div>
                <div><strong>Documento de Identidad:</strong> <span id="det-ci"></span></div>
                <div><strong>Modalidad Salarial:</strong> <span id="det-modalidad"></span></div>
                <div><strong>Fecha de Inicio:</strong> <span id="det-inicio"></span></div>
                <div><strong>Fecha de Término:</strong> <span id="det-fin"></span></div>
                <div><strong>Frecuencia de Liquidación:</strong> <span id="det-periodo"></span></div>
                <div><strong>Valor / Porcentaje Pactado:</strong> <span id="det-valor"
                        style="font-weight:700; color:var(--success);"></span></div>
            </div>

            {{-- Sección de Firmas (Oculta en web con 'only-print', visible en PDF/Impresión) --}}
            <div class="only-print"
                style="margin-top:5rem; justify-content:space-between; gap:2rem; text-align:center; font-size:.9rem; color:white;">
                <div style="width:45%;">
                    <div class="linea-firma"
                        style="border-top:1px dashed rgba(255,255,255,.4); margin-bottom:.5rem; padding-top:.5rem;">
                    </div>
                    <strong>JECOES Tronic</strong><br>
                    <span style="font-size:.8rem; color:var(--muted);">Empleador / Administrador</span>
                </div>
                <div style="width:45%;">
                    <div class="linea-firma"
                        style="border-top:1px dashed rgba(255,255,255,.4); margin-bottom:.5rem; padding-top:.5rem;">
                    </div>
                    <strong id="det-firma-nombre">Trabajador</strong><br>
                    <span style="font-size:.8rem; color:var(--muted);">Firma del Empleado</span>
                </div>
            </div>
        </div>

        {{-- Acciones del Modal (Se ocultan automáticamente en la impresión) --}}
        <div class="form-actions" style="margin-top:2rem; display:flex; gap:.5rem; justify-content:flex-end;">
            <button type="button" class="btn btn-ghost" onclick="cerrarModalVer()">Cerrar</button>
            <button type="button" class="btn btn-primary" onclick="window.print()">🖨 Imprimir Contrato</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function abrirModalCrear() {
        document.getElementById('modalContrato').style.display = 'flex';
    }
    function cerrarModalCrear() {
        document.getElementById('modalContrato').style.display = 'none';
    }
    
    document.getElementById('tipo_remuneracion').addEventListener('change', function() {
        let label = document.getElementById('labelValor');
        if(this.value == "1") {
            label.innerHTML = 'Sueldo Fijo (Bs.) <span class="req">*</span>';
        } else if(this.value == "2") {
            label.innerHTML = 'Porcentaje de Comisión (%) <span class="req">*</span>';
        } else {
            label.innerHTML = 'Monto Base / Porcentaje <span class="req">*</span>';
        }
    });

    function verContrato(id) {
        fetch(`/contratos/${id}/ver`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('det-id').innerText = `#${data.id}`;
                document.getElementById('det-nombre').innerText = data.personal.nombre;
                document.getElementById('det-ci').innerText = data.ci_personal;
                
                // Actualiza dinámicamente el nombre abajo de la línea de firma en la impresión
                document.getElementById('det-firma-nombre').innerText = data.personal.nombre;
                
                let descripcionMod = data.modalidad_remuneracion ? data.modalidad_remuneracion.descripcion : (data.tipo_remuneracion == 1 ? 'Sueldo Fijo' : 'Porcentaje por Comisión');
                document.getElementById('det-modalidad').innerText = descripcionMod;
                
                document.getElementById('det-inicio').innerText = data.fecha_inicio;
                document.getElementById('det-fin').innerText = data.fecha_fin ? data.fecha_fin : 'Indefinido';
                document.getElementById('det-periodo').innerText = data.periodo_pago ? data.periodo_pago : 'Semanal';
                
                let sufijo = data.tipo_remuneracion == 1 ? ' Bs.' : ' %';
                document.getElementById('det-valor').innerText = data.valor + sufijo;
                
                let elEstado = document.getElementById('det-estado');
                elEstado.innerText = data.estado;
                if(data.estado === 'Vigente') {
                    elEstado.style.color = '#2ecc71';
                } else {
                    elEstado.style.color = '#94a3b8';
                }

                document.getElementById('modalVerContrato').style.display = 'flex';
            })
            .catch(err => {
                console.error("Error al obtener contrato", err);
                alert("No se pudo cargar la información del contrato.");
            });
    }

    function cerrarModalVer() {
        document.getElementById('modalVerContrato').style.display = 'none';
    }
</script>
@endpush

@endsection