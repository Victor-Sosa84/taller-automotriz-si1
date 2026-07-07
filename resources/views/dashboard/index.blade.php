@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

{{-- Encabezado del Dashboard --}}
<div
    style="margin-bottom:1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h2
            style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; text-transform:uppercase;">
            Bienvenido, {{ auth()->user()->nombre_usuario }}
        </h2>
        <p style="color:var(--muted); font-size:.85rem; margin-top:.2rem;">
            {{ auth()->user()->nombre_rol }} — {{ now()->format('d/m/Y') }}
        </p>
    </div>

</div>

{{-- CU-22: Reportes por comando de voz (INTACTO, NO SE TOCA) --}}
@if(auth()->user()->puede('CU22_GEN'))
<div class="card" id="card-reporte-voz" style="margin-bottom:1.75rem;">
    <div class="card-header">
        <span class="card-title">🎙 Preguntá algo sobre el taller</span>
    </div>
    <div class="card-body">
        <div style="display:flex; align-items:center; gap:1rem; flex-wrap:wrap;">
            <button id="btn-grabar-voz" type="button" class="btn btn-primary">
                <span id="icono-grabar-voz">🎤</span>
                <span id="texto-grabar-voz">Hablar</span>
            </button>
            <span id="estado-voz" style="color:var(--muted); font-size:.85rem;"></span>
        </div>

        <div id="resultado-voz-wrap" style="margin-top:1.25rem; display:none;">
            <div style="margin-bottom:.75rem;">
                <div
                    style="font-size:.78rem; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); margin-bottom:.3rem;">
                    Entendí que preguntaste
                </div>
                <div id="texto-transcripcion" style="font-size:.95rem;"></div>
            </div>
            <div id="resultado-voz-contenido"></div>
            <audio id="audio-respuesta-voz" controls style="width:100%; margin-top:1rem; display:none;"></audio>
            <div id="acciones-exportar-voz" style="margin-top:1rem; display:none; gap:.5rem;">
                <button type="button" class="btn btn-ghost btn-sm" id="btn-exportar-pdf">⬇ Exportar PDF</button>
                <button type="button" class="btn btn-ghost btn-sm" id="btn-exportar-excel">⬇ Exportar Excel</button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- APARTADO DESPLEGABLE: GRAFICAS DINÁMICAS --}}
<div class="mb-4">
    <button class="btn btn-warning w-100 text-start d-flex justify-content-between align-items-center fw-bold"
        type="button" data-bs-toggle="collapse" data-bs-target="#seccionGraficas" aria-expanded="true"
        aria-controls="seccionGraficas"
        style="background-color: #f29436; border: none; color: #1e222b; padding: 12px 20px; border-radius: 6px;">
        <span>📊 APARTADO: GRAFICAS ANALÍTICAS MULTIPLES @if(request('fecha')) (Filtrado por Mes) @else (Histórico
            Global) @endif</span>
        <i class="fas fa-chevron-down"></i>
    </button>

    {{-- CU23: Formulario para aplicarFiltro() y exportar reporte --}}
    <div class="filter-box"
        style="background: var(--surface); padding: .75rem 1rem; border-radius: 6px; display: flex; gap: .75rem; align-items: center; border: 1px solid var(--border);">
        <form id="form-filtro-dashboard" action="{{ route('dashboard.index') }}" method="GET"
            style="display: flex; align-items: center; gap: .75rem; margin: 0;">
            <label for="fecha"
                style="font-size: .75rem; font-weight: 700; text-transform: uppercase; color: var(--muted);">Filtrar por
                Fecha:</label>
            {{-- Cambiamos el valor por defecto: si viene fecha en la URL se queda, sino se limpia para mostrar el
            histórico global --}}
            <input type="date" id="fecha" name="fecha" value="{{ request('fecha') }}"
                style="padding: .4rem; border-radius: 4px; border: 1px solid var(--border); background: transparent; color: inherit; font-size: .85rem;">
            <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
            @if(request('fecha'))
            <a href="{{ route('dashboard.index') }}" class="btn btn-ghost btn-sm"
                style="border: 1px solid var(--border);">Limpiar Filtro</a>
            @endif
        </form>

        {{-- Botón para descargar el reporte dashboard.pdf --}}
        <a href="{{ route('dashboard.reporte', ['fecha' => request('fecha', now()->format('Y-m-d'))]) }}"
            class="btn btn-ghost btn-sm"
            style="border: 1px solid var(--border); display: flex; align-items: center; gap: .3rem;">
            <span>📄</span> Exportar PDF
        </a>
    </div>

</div>

<div class="collapse show" id="seccionGraficas">
    <div
        style="background: var(--surface); border: 1px solid var(--border); border-radius: 6px; padding: 1.5rem; margin-bottom: 1.75rem;">

        <div class="row">
            {{-- 1. Líneas - Evolución de ingresos --}}
            <div class="col-md-8 mb-4">
                <div style="background: #1e222b; border-radius: 6px; border: 1px solid var(--border); padding: 1rem;">
                    <h5
                        style="color: #f29436; font-size: 0.95rem; text-transform: uppercase; margin-bottom: 1rem; font-weight: 700;">
                        📈 Evolución Temporal de Ingresos ($)
                    </h5>
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="chartIngresos"></canvas>
                    </div>
                </div>
            </div>

            {{-- 2. Dona - Estados de las órdenes --}}
            <div class="col-md-4 mb-4">
                <div style="background: #1e222b; border-radius: 6px; border: 1px solid var(--border); padding: 1rem;">
                    <h5
                        style="color: #f29436; font-size: 0.95rem; text-transform: uppercase; margin-bottom: 1rem; font-weight: 700;">
                        🍩 Carga de Trabajo (Estados)
                    </h5>
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="chartEstados"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- 3. Barras Horizontales - Mecánicos --}}
            <div class="col-md-6 mb-4 mb-md-0">
                <div style="background: #1e222b; border-radius: 6px; border: 1px solid var(--border); padding: 1rem;">
                    <h5
                        style="color: #f29436; font-size: 0.95rem; text-transform: uppercase; margin-bottom: 1rem; font-weight: 700;">
                        👨‍🔧 Top Mecánicos (Trabajos Realizados)
                    </h5>
                    <div style="position: relative; height: 280px; width: 100%;">
                        <canvas id="chartMecanicos"></canvas>
                    </div>
                </div>
            </div>

            {{-- 4. Barras Verticales - Repuestos --}}
            <div class="col-md-6">
                <div style="background: #1e222b; border-radius: 6px; border: 1px solid var(--border); padding: 1rem;">
                    <h5
                        style="color: #f29436; font-size: 0.95rem; text-transform: uppercase; margin-bottom: 1rem; font-weight: 700;">
                        📦 Top Repuestos de Mayor Rotación
                    </h5>
                    <div style="position: relative; height: 280px; width: 100%;">
                        <canvas id="chartRepuestos"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Stats globales del sistema --}}
@if($stats['totalUsuarios'] !== null || $stats['totalClientes'] !== null || $stats['totalPersonal'] !== null)
<div class="stats-grid" style="margin-bottom:1.75rem;">
    @if($stats['totalUsuarios'] !== null)
    <div class="stat-card">
        <div class="stat-label">Usuarios del sistema</div>
        <div class="stat-value">{{ $stats['totalUsuarios'] }}</div>
        <div class="stat-sub">Con acceso a la plataforma</div>
    </div>
    @endif
    @if($stats['totalPersonal'] !== null)
    <div class="stat-card">
        <div class="stat-label">Personal registrado</div>
        <div class="stat-value">{{ $stats['totalPersonal'] }}</div>
        <div class="stat-sub">Mecánicos y administrativos</div>
    </div>
    @endif
    @if($stats['totalClientes'] !== null)
    <div class="stat-card">
        <div class="stat-label">Clientes registrados</div>
        <div class="stat-value">{{ $stats['totalClientes'] }}</div>
        <div class="stat-sub">En la base de datos</div>
    </div>
    @endif
</div>
@endif

{{-- Bitácora reciente --}}
@if($ultimasBitacoras->isNotEmpty())
<div class="card">
    <div class="card-header">
        <span class="card-title">📋 Últimas acciones — Bitácora</span>
        <a href="{{ route('bitacora.index') }}" class="btn btn-ghost btn-sm">Ver todo</a>
    </div>
    <div class="table-wrap" style="border:none; border-radius:0;">
        <table>
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>IP</th>
                    <th>Fecha y hora</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ultimasBitacoras as $log)
                <tr>
                    <td style="font-weight:600;">{{ $log->usuario?->nombre_usuario ?? '—' }}</td>
                    <td>{{ $log->accion }}</td>
                    <td class="td-muted">{{ $log->ip_equipo ?? '—' }}</td>
                    <td class="td-muted">{{ $log->fecha_hora->format('d/m/Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div class="card">
    <div class="card-body" style="text-align:center; padding:3rem;">
        <div style="font-size:3rem; margin-bottom:1rem; opacity:.3;">⚙</div>
        <div
            style="font-family:'Barlow Condensed',sans-serif; font-size:1.3rem; font-weight:700; text-transform:uppercase; margin-bottom:.5rem;">
            Sistema listo
        </div>
        <div style="color:var(--muted); font-size:.9rem;">
            Usa el menú lateral para navegar entre los módulos disponibles.
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="{{ asset('js/reporte-voz.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Obtenemos el parámetro de fecha actual de la URL para pasárselo a la API asíncrona
        const urlParams = new URLSearchParams(window.location.search);
        const fechaFiltro = urlParams.get('fecha') || '';

        // Disparamos la carga de datos respetando de forma segura el estado de las fechas
        cargarDataGraficas(fechaFiltro);
    });

    function cargarDataGraficas(fechaSeleccionada) {
        fetch(`{{ route('dashboard.filtrar') }}?fecha=${fechaSeleccionada}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderizarGraficosMultiples(data);
                } else {
                    console.error("Error en respuesta de servidor:", data.message);
                }
            })
            .catch(err => console.error("Error crítico recuperando métricas relacionales:", err));
    }

    function renderizarGraficosMultiples(data) {
        const opcionesDarkComunes = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: { color: '#b2b9bf', font: { family: 'sans-serif', size: 11 } }
                }
            }
        };

        // 1. CHART: LÍNEAS (Evolución de Ingresos)
        const ctxIngresos = document.getElementById('chartIngresos').getContext('2d');
        new Chart(ctxIngresos, {
            type: 'line',
            data: {
                labels: data.reporte_ingresos.map(item => item.periodo || item.dia),
                datasets: [{
                    label: 'Recaudado ($)',
                    data: data.reporte_ingresos.map(item => item.total),
                    borderColor: '#f29436',
                    backgroundColor: 'rgba(242, 148, 54, 0.08)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.25
                }]
            },
            options: {
                ...opcionesDarkComunes,
                scales: {
                    x: { ticks: { color: '#888' }, grid: { color: 'rgba(255,255,255,0.04)' } },
                    y: { ticks: { color: '#888' }, grid: { color: 'rgba(255,255,255,0.04)' } }
                }
            }
        });

        // 2. CHART: DONA (Estados Operativos)
        const ctxEstados = document.getElementById('chartEstados').getContext('2d');
        new Chart(ctxEstados, {
            type: 'doughnut',
            data: {
                labels: data.reporte_estados.map(item => item.estado),
                datasets: [{
                    data: data.reporte_estados.map(item => item.total),
                    backgroundColor: ['#f29436', '#36a2eb', '#e74a3b', '#28a745', '#ffc107', '#6c757d'],
                    borderWidth: 0
                }]
            },
            options: opcionesDarkComunes
        });

        // 3. CHART: BARRAS HORIZONTALES (Rendimiento Realiza)
        const ctxMecanicos = document.getElementById('chartMecanicos').getContext('2d');
        new Chart(ctxMecanicos, {
            type: 'bar',
            data: {
                labels: data.reporte_mecanicos.map(item => item.nombre),
                datasets: [{
                    label: 'Órdenes Concluidas',
                    data: data.reporte_mecanicos.map(item => item.trabajos_realizados),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: '#36a2eb',
                    borderWidth: 1
                }]
            },
            options: {
                ...opcionesDarkComunes,
                indexAxis: 'y',
                scales: {
                    x: { ticks: { color: '#888', stepSize: 1 }, grid: { color: 'rgba(255,255,255,0.04)' } },
                    y: { ticks: { color: '#888' }, grid: { display: false } }
                }
            }
        });

        // 4. CHART: BARRAS VERTICALES (Detalle Repuestos)
        const ctxRepuestos = document.getElementById('chartRepuestos').getContext('2d');
        new Chart(ctxRepuestos, {
            type: 'bar',
            data: {
                labels: data.reporte_repuestos.map(item => item.nombre),
                datasets: [{
                    label: 'Unidades Vendidas/Instaladas',
                    data: data.reporte_repuestos.map(item => item.total_usado),
                    backgroundColor: 'rgba(231, 74, 59, 0.6)',
                    borderColor: '#e74a3b',
                    borderWidth: 1
                }]
            },
            options: {
                ...opcionesDarkComunes,
                scales: {
                    x: { ticks: { color: '#888' }, grid: { display: false } },
                    y: { ticks: { color: '#888', stepSize: 1 }, grid: { color: 'rgba(255,255,255,0.04)' } }
                }
            }
        });
    }
</script>
@endpush