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
            <p style="color: #888; margin: 0; font-size: 0.9rem;">Módulo de operaciones — Cierre de Ciclo de Servicio
            </p>
        </div>
        <span style="color: #888; font-size: 0.85rem;">{{ date('d/m/Y H:i') }}</span>
    </div>

    {{-- BLOQUE 1: Buscador de Orden de Trabajo --}}
    <div class="card" style="margin-bottom: 1.75rem; background-color: #1e1e24; border: 1px solid #2d2d35;">
        <div class="card-header"
            style="background: rgba(255,255,255,0.02); border-bottom: 1px solid #2d2d35; padding: 0.75rem 1.25rem;">
            <span class="card-title"
                style="font-weight: 600; color: #f29436; text-transform: uppercase; font-size: 0.95rem;">🔍 Buscar Orden
                de Trabajo Activa</span>
        </div>
        <div class="card-body" style="padding: 1.25rem;">
            <form id="formBuscarOrden" class="row g-3 align-items-end" onsubmit="return false;">
                <div class="col-md-6">
                    <label for="nro_orden" class="form-label"
                        style="color: #bcbcbc; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem; display: block;">
                        Número de Orden o Placa del Auto
                    </label>
                    <input type="text" id="nro_orden" class="form-control" placeholder="Ej: 1024 o 4521-XYZ"
                        style="background-color: #15151a; border: 1px solid #3a3a45; color: #fff; width: 100%; padding: 0.5rem 0.75rem; border-radius: 4px;">
                </div>
                <div class="col-md-3">
                    <button type="button" id="btnVerificar" class="btn w-100"
                        style="background-color: #f29436; border: none; color: #000; font-weight: 700; text-transform: uppercase; padding: 0.5rem; border-radius: 4px; letter-spacing: 0.5px;">
                        Verificar Estado
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Contenedor de Información Dinámica --}}
    <div id="wrapperDetalleEntrega" style="display: none;">
        <div class="row g-4">

            {{-- BLOQUE 2: Estado de Validaciones (Izquierda) --}}
            <div class="col-md-5">
                <div class="card"
                    style="margin-bottom: 1.75rem; background-color: #1e1e24; border: 1px solid #2d2d35; height: 100%;">
                    <div class="card-header" style="border-bottom: 1px solid #2d2d35; padding: 0.75rem 1.25rem;">
                        <span class="card-title"
                            style="font-weight: 600; color: #fff; text-transform: uppercase; font-size: 0.95rem;">📋
                            Estado Técnico y Financiero</span>
                    </div>
                    <div class="card-body" style="padding: 1.25rem;">
                        {{-- Validación Mecánica --}}
                        <div id="wrapperMecanico" class="d-flex align-items-center justify-content-between p-3"
                            style="background: #15151a; border-radius: 4px; margin-bottom: 1.25rem; border-left: 4px solid #28a745;">
                            <div>
                                <h5 style="margin:0; font-size:0.95rem; color:#fff; font-weight: 600;">Estado Mecánico
                                    de la Orden</h5>
                                <small id="lblOrdenSub"
                                    style="color: #888; font-size: 0.8rem; display: block; margin-top: 2px;"></small>
                            </div>
                            <span id="badgeMecanico" class="badge"
                                style="padding: 0.5rem 0.75rem; font-size: 0.75rem; font-weight: bold;">FINALIZADA</span>
                        </div>

                        {{-- Validación Financiera --}}
                        <div id="wrapperFinanciero" class="d-flex align-items-center justify-content-between p-3"
                            style="background: #15151a; border-radius: 4px; border-left: 4px solid #28a745;">
                            <div>
                                <h5 style="margin:0; font-size:0.95rem; color:#fff; font-weight: 600;">Estado de Cuentas
                                    (Cuotas)</h5>
                                <small id="lblFinanzasSub"
                                    style="color: #888; font-size: 0.8rem; display: block; margin-top: 2px;">Saldo
                                    pendiente del cliente</small>
                            </div>
                            <span id="badgeFinanciero" class="badge"
                                style="padding: 0.5rem 0.75rem; font-size: 0.75rem; font-weight: bold;">AL DÍA /
                                SALDADO</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BLOQUE 3: Datos de Recepción - Tabla Recoge (Derecha) --}}
            <div class="col-md-7">
                <div class="card" style="margin-bottom: 1.75rem; background-color: #1e1e24; border: 1px solid #2d2d35;">
                    <div class="card-header" style="border-bottom: 1px solid #2d2d35; padding: 0.75rem 1.25rem;">
                        <span class="card-title"
                            style="font-weight: 600; color: #fff; text-transform: uppercase; font-size: 0.95rem;">✍️
                            Información de la Persona que Recoge</span>
                    </div>
                    <div class="card-body" style="padding: 1.5rem;">
                        <form id="formRegistrarSalida">

                            {{-- Fila CI y Nombre --}}
                            <div class="row g-3" style="margin-bottom: 1.25rem;">
                                <div class="col-md-4">
                                    <label class="form-label"
                                        style="color: #bcbcbc; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem; display: block;">
                                        Cédula de Identidad (CI)
                                    </label>
                                    <input type="text" id="ci_persona" required class="form-control"
                                        style="background-color: #15151a; border: 1px solid #3a3a45; color: #fff; width: 100%; padding: 0.5rem 0.75rem; border-radius: 4px;">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label"
                                        style="color: #bcbcbc; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem; display: block;">
                                        Nombre Completo del Receptor
                                    </label>
                                    <input type="text" id="nombre_persona" required class="form-control"
                                        style="background-color: #15151a; border: 1px solid #3a3a45; color: #fff; width: 100%; padding: 0.5rem 0.75rem; border-radius: 4px;">
                                </div>
                            </div>

                            {{-- Fila Parentesco --}}
                            <div class="form-group" style="margin-bottom: 1.25rem;">
                                <label class="form-label"
                                    style="color: #bcbcbc; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem; display: block;">
                                    Parentesco / Relación con el Propietario
                                </label>
                                <select id="relacion" class="form-select"
                                    style="background-color: #15151a; border: 1px solid #3a3a45; color: #fff; width: 100%; padding: 0.5rem 0.75rem; border-radius: 4px;">
                                    <option value="Propietario">Es el Propietario</option>
                                    <option value="Esposa/o">Esposa/o</option>
                                    <option value="Hijo/a">Hijo/a</option>
                                    <option value="Hermano/a">Hermano/a</option>
                                    <option value="Chofer">Chofer de la Empresa</option>
                                    <option value="Otro">Otro Familiar / Conocido</option>
                                </select>
                            </div>

                            {{-- Fila Observaciones --}}
                            <div class="form-group" style="margin-bottom: 1.5rem;">
                                <label class="form-label"
                                    style="color: #bcbcbc; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem; display: block;">
                                    Observaciones de Entrega
                                </label>
                                <textarea id="observaciones" rows="3" class="form-control"
                                    placeholder="Ej: Se entrega lavado, con las piezas sustituidas en la maletera..."
                                    style="background-color: #15151a; border: 1px solid #3a3a45; color: #fff; width: 100%; padding: 0.5rem 0.75rem; border-radius: 4px; resize: none;"></textarea>
                            </div>

                            {{-- Acción Final --}}
                            <div class="form-group">
                                <button type="submit" id="btnGuardarSalida" class="btn"
                                    style="background-color: #f29436; color: #000; font-weight: 700; text-transform: uppercase; padding: 0.75rem 1.5rem; border: none; border-radius: 4px; width: 100%; letter-spacing: 0.5px;">
                                    💾 Confirmar Registro de Salida e Imprimir Acta
                                </button>
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
        const formRegistrarSalida = document.getElementById('formRegistrarSalida');
        
        // Selector seguro para el token CSRF de Laravel
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        btnVerificar.addEventListener('click', function() {
            const inputNro = document.getElementById('nro_orden').value;
            if(!inputNro) return alert('Por favor ingresa un número de orden o placa');
            
            fetch("{{ route('salida.verificar') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({ nro_orden: inputNro })
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(resObj => {
                const data = resObj.body;
                
                if(resObj.status === 200 && data.success) {
                    document.getElementById('lblOrdenSub').innerText = "Orden #" + data.orden_nro + " — " + data.cliente_nombre;
                    
                    const badgeMec = document.getElementById('badgeMecanico');
                    const wrapMec = document.getElementById('wrapperMecanico');
                    
                    if(badgeMec && wrapMec) {
                        badgeMec.innerText = data.estado_mecanico;
                        if(data.es_finalizada) {
                            badgeMec.className = "badge bg-success";
                            wrapMec.style.borderLeft = "4px solid #28a745";
                        } else {
                            badgeMec.className = "badge bg-danger";
                            wrapMec.style.borderLeft = "4px solid #dc3545";
                        }
                    }
                    
                    const badgeFin = document.getElementById('badgeFinanciero');
                    const wrapFin = document.getElementById('wrapperFinanciero');
                    
                    if(badgeFin && wrapFin) {
                        if(data.todo_saldado) {
                            badgeFin.innerText = "AL DÍA / SALDADO";
                            badgeFin.className = "badge bg-success";
                            wrapFin.style.borderLeft = "4px solid #28a745";
                            document.getElementById('lblFinanzasSub').innerText = "Sin cuentas pendientes";
                        } else {
                            badgeFin.innerText = "PENDIENTE DE PAGO";
                            badgeFin.className = "badge bg-danger";
                            wrapFin.style.borderLeft = "4px solid #dc3545";
                            document.getElementById('lblFinanzasSub').innerText = "El cliente presenta saldo deudor";
                        }
                    }
                    
                    wrapperDetalle.style.display = 'block';
                } else {
                    alert(data.message || 'No se encontró la orden o hubo un problema.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error crítico de red o de comunicación con el servidor.');
            });
        }); // <-- AQUÍ ESTABA EL ERROR (Cerrado correcto del EventListener)

        formRegistrarSalida.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const payload = {
                nro_orden: document.getElementById('nro_orden').value,
                ci_persona: document.getElementById('ci_persona').value,
                nombre_persona: document.getElementById('nombre_persona').value,
                relacion: document.getElementById('relacion').value,
                observaciones: document.getElementById('observaciones').value,
            };

            fetch("{{ route('salida.registrar') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert(data.message);
                    window.open(`/salida-vehiculos/imprimir/${data.nro_orden}`, '_blank');
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error crítico al procesar la salida.');
            });
        });
    });
</script>
@endpush