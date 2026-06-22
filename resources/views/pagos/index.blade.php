@extends('layouts.app')
@section('title', 'Liquidación de Pagos')

@section('content')

<div
    style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
    <div>
        <h2
            style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; text-transform:uppercase;">
            Liquidar Pagos de Personal
        </h2>
        <p style="color:var(--muted); font-size:.85rem; margin-top:.2rem;">
            Procesamiento de remuneraciones fijas y comisiones por porcentaje de mano de obra para JECOES Tronic.
        </p>
    </div>
    <button class="btn btn-primary" onclick="abrirModalLiquidar()">⚙ Procesar Nueva Liquidación</button>
</div>

{{-- Tabla de Historial de Pagos --}}
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <div class="card-title">Historial de Haberes Liquidados</div>
    </div>
    <div class="table-wrap" style="border: none; border-radius: 0;">
        <table>
            <thead>
                <tr>
                    <th>ID Pago</th>
                    <th>Empleado (CI)</th>
                    <th>Contrato ref.</th>
                    <th>Fecha de Pago</th>
                    <th>Monto Liquidado</th>
                    <th>Tipo</th>
                    <th>Método</th>
                    <th style="text-align:center;">Constancia</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pagos as $pago)
                <tr>
                    <td class="td-muted" style="font-family:monospace;">#{{ $pago->id }}</td>
                    <td style="font-weight:600;">
                        {{ $pago->contrato->personal->nombre ?? 'N/A' }}
                        <span class="td-muted" style="font-size:.75rem; font-family:monospace; display:block;">
                            CI: {{ $pago->contrato->ci_personal ?? $pago->contrato->personal->ci ?? '' }}
                        </span>
                    </td>
                    <td class="td-muted" style="font-family:monospace;">Contrato #{{ $pago->id_contrato }}</td>
                    <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y H:i') }}</td>
                    <td style="font-weight:700; color:var(--success);">{{ number_format($pago->monto, 2) }} Bs.</td>
                    <td><span class="badge badge-recep">{{ $pago->tipo ?? 'Sueldo' }}</span></td>
                    <td><span class="badge badge-mec" style="background:rgba(255,255,255,.05); color:var(--text);">{{
                            $pago->metodo ?? 'Efectivo' }}</span></td>
                    <td style="text-align:center;">
                        <button
                            onclick="imprimirConstanciaDirecta({{ $pago->id }}, '{{ $pago->contrato->personal->nombre ?? 'Empleado' }}', '{{ $pago->monto }}')"
                            class="btn btn-sm btn-ghost">🖨 Imprimir</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:2.5rem; color:var(--muted);">
                        No se registran liquidaciones financieras en este periodo.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL PARA PROCESAR LIQUIDACIÓN --}}
<div id="modalLiquidacion"
    style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.75); z-index:1000; align-items:center; justify-content:center; padding:1rem;">
    <div class="form-card" style="width:100%; max-width:650px; max-height:90vh; overflow-y:auto;">
        <h3
            style="font-family:'Barlow Condensed',sans-serif; font-size:1.4rem; font-weight:700; text-transform:uppercase; margin-bottom:1.5rem; color:var(--accent);">
            Nueva Liquidación de Haberes
        </h3>

        <form action="{{ route('pagos.store') }}" method="POST">
            @csrf
            <div class="form-grid" style="grid-template-columns: 1fr;">
                <div class="field-group">
                    <label>Seleccionar Contrato Vigente del Personal <span class="req">*</span></label>
                    <select name="id_contrato" id="id_contrato_select" required onchange="cargarPrecalculo(this.value)">
                        <option value="">-- Seleccione un Empleado con Contrato Activo --</option>
                        @foreach($contratosVigentes as $con)
                        <option value="{{ $con->id }}">
                            {{ $con->personal->nombre }} (Contrato #{{ $con->id }} - {{ $con->periodo_pago ?? 'Mensual'
                            }})
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Contenedor del Pre-cálculo asíncrono (BCE Logic) --}}
            <div id="area_calculo"
                style="display:none; margin-top:1.25rem; background:var(--surface2); border:1px solid var(--border); padding:1rem; border-radius:var(--radius);">
                <h4
                    style="font-size:.85rem; text-transform:uppercase; color:var(--accent); margin-bottom:.75rem; font-weight:700;">
                    📊 Desglose de Haberes Calculado (En Base a Datos)
                </h4>
                <p style="font-size:.9rem; margin-bottom:.5rem;">Modalidad contractual: <span id="calc_modalidad"
                        style="font-weight:600;"></span></p>
                <p style="font-size:.9rem; margin-bottom:.5rem;">Monto base estipulado: <span id="calc_base"
                        style="font-weight:600; color:var(--accent);"></span></p>

                {{-- Sección dinámica para mecánicos (Mano de obra acumulada desde la tabla realiza) --}}
                <div id="area_detalles_comision"
                    style="display:none; margin-top:.75rem; padding-top:.75rem; border-top:1px dashed var(--border);">
                    <p
                        style="font-size:.8rem; color:var(--muted); font-weight:600; text-transform:uppercase; margin-bottom:.5rem;">
                        Trabajos del periodo (Tabla Realiza):
                    </p>
                    <ul id="lista_trabajos"
                        style="font-size:.85rem; padding-left:1.2rem; color:var(--muted); display:flex; flex-direction:column; gap:.25rem;">
                    </ul>
                </div>

                <div
                    style="margin-top:1rem; padding-top:.75rem; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                    <span
                        style="font-family:'Barlow Condensed',sans-serif; font-weight:700; text-transform:uppercase; font-size:1.1rem;">Total
                        Neto a Liquidar:</span>
                    <span id="calc_total"
                        style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; color:var(--success);">0.00
                        Bs.</span>
                </div>
            </div>

            <div class="form-grid" style="margin-top:1.25rem;">
                <div class="field-group">
                    <label>Tipo de Pago <span class="req">*</span></label>
                    <select name="tipo" required>
                        <option value="Sueldo Mensual" selected>Sueldo Mensual</option>
                        <option value="Adelanto / Quincena">Adelanto / Quincena</option>
                        <option value="Liquidación Final">Liquidación Final</option>
                    </select>
                </div>
                <div class="field-group">
                    <label>Método de Desembolso <span class="req">*</span></label>
                    <select name="metodo" required>
                        <option value="Efectivo en Caja" selected>Efectivo en Caja</option>
                        <option value="Transferencia Bancaria">Transferencia Bancaria</option>
                        <option value="Cheque Gerencial">Cheque Gerencial</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-ghost" onclick="cerrarModalLiquidar()">Cancelar</button>
                <button type="submit" id="btn_guardar_pago" class="btn btn-primary" disabled>Confirmar y Registrar
                    Pago</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function abrirModalLiquidar() {
        document.getElementById('modalLiquidacion').style.display = 'flex';
    }
    
    function cerrarModalLiquidar() {
        document.getElementById('modalLiquidacion').style.display = 'none';
        document.getElementById('area_calculo').style.display = 'none';
        document.getElementById('id_contrato_select').value = "";
        document.getElementById('btn_guardar_pago').disabled = true;
    }

    // Lógica asíncrona que invoca al controlador de pagos
    function cargarPrecalculo(id_contrato) {
        if (!id_contrato) {
            document.getElementById('area_calculo').style.display = 'none';
            document.getElementById('btn_guardar_pago').disabled = true;
            return;
        }

        fetch(`/pagos/${id_contrato}/calcular`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('area_calculo').style.display = 'block';
                
                // Validación del objeto de modalidad de remuneración
                if (data.contrato && data.contrato.modalidad_remuneracion) {
                    document.getElementById('calc_modalidad').innerText = data.contrato.modalidad_remuneracion.descripcion;
                } else {
                    document.getElementById('calc_modalidad').innerText = "Estipulado por Contrato";
                }
                
                let unidad = (data.contrato && data.contrato.tipo_remuneracion == 1) ? 'Bs.' : '%';
                let valorBase = data.contrato ? parseFloat(data.contrato.valor).toFixed(2) : '0.00';
                
                document.getElementById('calc_base').innerText = `${valorBase} ${unidad}`;
                document.getElementById('calc_total').innerText = `${parseFloat(data.monto_calculado).toFixed(2)} Bs.`;
                
                let areaComision = document.getElementById('area_detalles_comision');
                let listaTrabajos = document.getElementById('lista_trabajos');
                listaTrabajos.innerHTML = "";

                if (data.detalles && data.detalles.length > 0) {
                    areaComision.style.display = 'block';
                    data.detalles.forEach(t => {
                        let li = document.createElement('li');
                        // Se unifican posibles mapeos de índices ('orden' o 'order')
                        let numeroOrden = t.orden || t.order || 'N/A';
                        li.innerHTML = `Orden OT #${numeroOrden} - ${t.servicio} (Comisión: <span style="color:var(--success)">+${parseFloat(t.comision_calculada).toFixed(2)} Bs.</span>)`;
                        listaTrabajos.appendChild(li);
                    });
                } else {
                    areaComision.style.display = 'none';
                }

                document.getElementById('btn_guardar_pago').disabled = false;
            })
            .catch(err => {
                console.error("Error al procesar el pre-cálculo:", err);
                alert("Ocurrió un error al conectar con la base de datos de comisiones.");
            });
    }

    // Ventana automatizada para impresión de constancia de firmas
    function imprimirConstanciaDirecta(id, nombre, monto) {
        let ventanaImpresion = window.open('', '_blank', 'width=800,height=600');
        ventanaImpresion.document.write(`
            <html>
            <head>
                <title>Constancia de Pago - JECOES Tronic</title>
                <style>
                    body { font-family: 'Helvetica', sans-serif; padding: 40px; color: #111; line-height: 1.6; }
                    .header { text-align: center; border-bottom: 2px solid #222; padding-bottom: 15px; margin-bottom: 30px; }
                    .title { font-size: 20px; font-weight: bold; text-transform: uppercase; }
                    .sub { font-size: 12px; color: #555; }
                    .content { margin-bottom: 50px; font-size: 15px; }
                    .firma-container { display: flex; justify-content: space-between; margin-top: 100px; padding: 0 40px; }
                    .linea-firma { border-top: 1px solid #000; width: 220px; text-align: center; padding-top: 5px; font-size: 13px; font-weight: bold; }
                </style>
            </head>
            <body>
                <div class="header">
                    <div class="title">Constancia de Recepción de Haberes</div>
                    <div class="sub">SISTEMA WEB JECOES TRONIC - COMPROBANTE FINANCIERO DE PAGO #${id}</div>
                </div>
                <div class="content">
                    <p>Por medio del presente documento legal, se deja constancia formal de que la empresa <strong>JECOES Tronic</strong> ha realizado el desembolso financiero correspondiente a los haberes laborados a favor de:</p>
                    <p style="margin-left: 20px; margin-top: 15px;"><strong>Nombre del Empleado:</strong> ${nombre}</p>
                    <p style="margin-left: 20px;"><strong>Monto Total Liquidado:</strong> ${parseFloat(monto).toFixed(2)} Bs.</p>
                    <p style="margin-left: 20px;"><strong>Fecha de Procesamiento:</strong> ${new Date().toLocaleDateString()}</p>
                    <p style="margin-top: 25px;">El trabajador declara estar conforme con el desglose financiero detallado y el importe recibido, no teniendo ningún reclamo posterior sobre el periodo liquidado.</p>
                </div>
                <div class="firma-container">
                    <div class="linea-firma">Administración<br>JECOES Tronic</div>
                    <div class="linea-firma">Firma del Empleado<br>C.I. Personal</div>
                </div>
                <script>
                    window.onload = function() { window.print(); window.close(); }
                <\/script>
            </body>
            </html>
        `);
        ventanaImpresion.document.close();
    }

    // Disparador automático tras el almacenamiento del controlador de Laravel
    @if(session('imprimir'))
        document.addEventListener('DOMContentLoaded', function() {
            imprimirConstanciaDirecta(
                "{{ session('pago_id') }}", 
                "Liquidación Reciente", 
                "{{ session('monto_pago') ?? '0' }}"
            );
        });
    @endif
</script>
@endpush

@endsection