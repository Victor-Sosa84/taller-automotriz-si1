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

    {{-- CU23: Formulario para aplicarFiltro() y exportar reporte --}}
    <div class="filter-box"
        style="background: var(--surface); padding: .75rem 1rem; border-radius: 6px; display: flex; gap: .75rem; align-items: center; border: 1px solid var(--border);">
        <form id="form-filtro-dashboard" action="{{ route('dashboard.index') }}" method="GET"
            style="display: flex; align-items: center; gap: .75rem; margin: 0;">
            <label for="fecha"
                style="font-size: .75rem; font-weight: 700; text-transform: uppercase; color: var(--muted);">Filtrar por
                Fecha:</label>
            <input type="date" id="fecha" name="fecha" value="{{ request('fecha', now()->format('Y-m-d')) }}"
                style="padding: .4rem; border-radius: 4px; border: 1px solid var(--border); background: transparent; color: inherit; font-size: .85rem;">
            <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
        </form>

        {{-- Botón para descargar el reporte dashboard.pdf --}}
        <a href="{{ route('dashboard.reporte', ['fecha' => request('fecha', now()->format('Y-m-d'))]) }}"
            class="btn btn-ghost btn-sm"
            style="border: 1px solid var(--border); display: flex; align-items: center; gap: .3rem;">
            <span>📄</span> Exportar PDF
        </a>
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

{{-- CU23: Panel Gráfico (+grafico) --}}
<div class="card" style="margin-bottom: 1.75rem;">
    <div class="card-header">
        <span class="card-title">📊 Gráfico de Rendimiento Operativo</span>
    </div>
    <div class="card-body" style="height: 280px; position: relative; width: 100%;">
        <canvas id="dashboardChart"></canvas>
    </div>
</div>

{{-- CU23: Métricas adicionales calculadas --}}
@if(isset($metricasAdicionales))
<div style="margin-bottom:1.75rem;">
    <h3
        style="font-family:'Barlow Condensed',sans-serif; font-size:1.1rem; font-weight:700; text-transform:uppercase; margin-bottom:.75rem; color: var(--muted);">
        📈 Indicadores del Diagrama de Clases (Orden, Cuota, Repuestos, Realiza)
    </h3>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Órdenes de Trabajo</div>
            <div class="stat-value">{{ $metricasAdicionales['totalOrdenes'] ?? 0 }}</div>
            <div class="stat-sub">En estado activo/procesado</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Ingresos por Cuotas</div>
            <div class="stat-value">${{ number_format($metricasAdicionales['ingresosCuotas'] ?? 0, 2) }}</div>
            <div class="stat-sub">Total recaudado en caja</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Repuestos Utilizados</div>
            <div class="stat-value">{{ $metricasAdicionales['totalRepuestosUsados'] ?? 0 }}</div>
            <div class="stat-sub">Detalle de repuestos cargados</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Mecánicos Asignados</div>
            <div class="stat-value">{{ $metricasAdicionales['mecanicosActivos'] ?? 0 }}</div>
            <div class="stat-sub">Flujo operativo (Realiza)</div>
        </div>
    </div>
</div>
@endif

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
{{-- Scripts del CU22 Comando de Voz (Preservado) --}}
<script src="{{ asset('js/reporte-voz.js') }}"></script>

{{-- Scripts del CU23 Gráficos Estadísticos --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('dashboardChart').getContext('2d');
        
        // CORRECCIÓN: Cambiado de $metricasAdicionales a $metricas
        const datosChart = {
            ordenes: {{ $metricas['totalOrdenes'] ?? 0 }},
            cuotas: {{ $metricas['ingresosCuotas'] ?? 0 }},
            repuestos: {{ $metricas['totalRepuestosUsados'] ?? 0 }},
            mecanicos: {{ $metricas['mecanicosActivos'] ?? 0 }}
        };











        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Órdenes', 'Ingresos ($)', 'Repuestos', 'Mecánicos (Realiza)'],
                datasets: [{
                    label: 'Indicadores CU23',
                    data: [datosChart.ordenes, datosChart.cuotas, datosChart.repuestos, datosChart.mecanicos],
                    backgroundColor: [
                        'rgba(242, 148, 54, 0.4)', // Naranja institucional del taller
                        'rgba(54, 162, 235, 0.4)',
                        'rgba(255, 99, 132, 0.4)',
                        'rgba(75, 192, 192, 0.4)'
                    ],
                    borderColor: ['#f29436', '#36a2eb', '#ff6384', '#4bc1c2'],
                    borderWidth: 1.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255, 255, 255, 0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
</script>
@endpush