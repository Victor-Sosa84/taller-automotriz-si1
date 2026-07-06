@extends('layouts.app') {{-- O el layout base que utilices --}}

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">

    {{-- Encabezado del Módulo --}}
    <div class="d-flex justify-content-between align-items-center" style="margin-bottom: 1.75rem;">
        <div>
            <h2
                style="font-family: 'Barlow Condensed', sans-serif; font-size: 1.8rem; font-weight: 700; text-transform: uppercase; margin: 0; color: #fff;">
                🚗 Registrar Salida y Entrega de Vehículo
            </h2>
            <p style="color: var(--muted); margin: 0; font-size: 0.9rem;">Módulo de operaciones — Cierre de Ciclo de
                Servicio</p>
        </div>
        <span style="color: var(--muted); font-size: 0.85rem;">{{ date('d/m/Y H:i') }}</span>
    </div>

    {{-- BLOQUE 1: Buscador de Orden de Trabajo --}}
    <div class="card" style="margin-bottom: 1.75rem; background-color: #1e1e24; border: 1px solid #2d2d35;">
        <div class="card-header"
            style="background: rgba(255,255,255,0.02); border-bottom: 1px solid #2d2d35; padding: 0.75rem 1.25rem;">
            <span class="card-title" style="font-weight: 600; color: #f29436;">🔍 Buscar Orden de Trabajo Activa</span>
        </div>
        <div class="card-body" style="padding: 1.25rem;">
            <form id="formBuscarOrden" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="nro_orden" class="form-label"
                        style="color: #bcbcbc; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">
                        Número de Orden o Placa del Auto
                    </label>
                    <input type="text" id="nro_orden" class="form-control" placeholder="Ej: 1024 o 4521-XYZ"
                        style="background-color: #15151a; border: 1px solid #3a3a45; color: #fff; padding: 0.45rem 0.75rem;">
                </div>
                <div class="col-md-3">
                    <button type="button" id="btnVerificar" class="btn btn-warning w-100"
                        style="background-color: #f29436; border: none; color: #000; font-weight: 700; text-transform: uppercase; padding: 0.5rem;">
                        Verificar Estado
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Contenedor de Información Dinámica (Se muestra al encontrar la orden) --}}
    <div id="wrapperDetalleEntrega" style="display: none;">
        <div class="row">

            {{-- BLOQUE 2: Estado de Validaciones (Izquierda) --}}
            <div class="col-md-5">
                <div class="card" style="margin-bottom: 1.75rem; background-color: #1e1e24; border: 1px solid #2d2d35;">
                    <div class="card-header" style="border-bottom: 1px solid #2d2d35; padding: 0.75rem 1.25rem;">
                        <span class="card-title" style="font-weight: 600; color: #fff;">📋 Estado Técnico y
                            Financiero</span>
                    </div>
                    <div class="card-body" style="padding: 1.25rem;">
                        {{-- Validación Mecánica --}}
                        <div class="d-flex align-items-center justify-content-between p-3 style-condicion"
                            style="background: #15151a; border-radius: 4px; margin-bottom: 1rem;">
                            <div>
                                <h5 style="margin:0; font-size:0.95rem; color:#fff;">Estado Mecánico de la Orden</h5>
                                <small id="lblOrdenSub" style="color:var(--muted);"></small>
                            </div>
                            <span id="badgeMecanico" class="badge bg-success"
                                style="padding: 0.5rem 0.75rem; font-size: 0.8rem;">FINALIZADA</span>
                        </div>

                        {{-- Validación Financiera --}}
                        <div class="d-flex align-items-center justify-content-between p-3 style-condicion"
                            style="background: #15151a; border-radius: 4px;">
                            <div>
                                <h5 style="margin:0; font-size:0.95rem; color:#fff;">Estado de Cuentas (Cuotas)</h5>
                                <small id="lblFinanzasSub" style="color:var(--muted);">Saldo pendiente del
                                    cliente</small>
                            </div>
                            <span id="badgeFinanciero" class="badge bg-success"
                                style="padding: 0.5rem 0.75rem; font-size: 0.8rem;">AL DÍA / SALDADO</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BLOQUE 3: Datos de Recepción - Tabla Recoge (Derecha) --}}
            <div class="col-md-7">
                <div class="card" style="margin-bottom: 1.75rem; background-color: #1e1e24; border: 1px solid #2d2d35;">
                    <div class="card-header" style="border-bottom: 1px solid #2d2d35; padding: 0.75rem 1.25rem;">
                        <span class="card-title" style="font-weight: 600; color: #fff;">✍️ Información de la Persona que
                            Recoge</span>
                    </div>
                    <div class="card-body" style="padding: 1.25rem;">
                        <form id="formRegistrarSalida">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" style="color: #bcbcbc; font-size: 0.85rem;">Cédula de
                                        Identidad (CI)</label>
                                    <input type="text" id="ci_persona" required class="form-control"
                                        style="background-color: #15151a; border: 1px solid #3a3a45; color: #fff;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="color: #bcbcbc; font-size: 0.85rem;">Nombre
                                        Completo del Receptor</label>
                                    <input type="text" id="nombre_persona" required class="form-control"
                                        style="background-color: #15151a; border: 1px solid #3a3a45; color: #fff;">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label" style="color: #bcbcbc; font-size: 0.85rem;">Parentesco /
                                        Relación con el Propietario</label>
                                    <select id="relacion" class="form-select"
                                        style="background-color: #15151a; border: 1px solid #3a3a45; color: #fff;">
                                        <option value="Propietario">Es el Propietario</option>
                                        <option value="Esposa/o">Esposa/o</option>
                                        <option value="Hijo/a">Hijo/a</option>
                                        <option value="Hermano/a">Hermano/a</option>
                                        <option value="Chofer">Chofer de la Empresa</option>
                                        <option value="Otro">Otro Familiar / Conocido</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label" style="color: #bcbcbc; font-size: 0.85rem;">Observaciones
                                        de Entrega</label>
                                    <textarea id="observaciones" rows="2" class="form-control"
                                        placeholder="Ej: Se entrega lavado, con las piezas sustituidas en la maletera..."
                                        style="background-color: #15151a; border: 1px solid #3a3a45; color: #fff;"></textarea>
                                </div>

                                {{-- Acción Final --}}
                                <div class="col-md-12 style-acciones" style="margin-top: 1.5rem;">
                                    <button type="submit" id="btnGuardarSalida" class="btn btn-success w-100"
                                        style="font-weight:700; text-transform:uppercase; padding:0.6rem;">
                                        💾 Confirmar Registro de Salida e Imprimir Acta
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const btnVerificar = document.getElementById('btnVerificar');
        const wrapperDetalle = document.getElementById('wrapperDetalleEntrega');
        
        // Simulación rápida de comportamiento Frontend
        btnVerificar.addEventListener('click', function() {
            const input = document.getElementById('nro_orden').value;
            if(!input) return alert('Por favor ingresa un número de orden o placa');
            
            // Simular que encuentra los datos del diagrama robusto e interactúa
            document.getElementById('lblOrdenSub').innerText = "Orden #" + input + " — Auto Verificado";
            wrapperDetalle.style.display = 'block'; // Abre la interfaz de abajo
        });

        document.getElementById('formRegistrarSalida').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('¡Salida Guardada con éxito en la tabla RECOGE! Generando Acta de Entrega PDF...');
            // Aquí disparas tu ruta AJAX hacia el controlador
        });
    });
</script>
@endpush