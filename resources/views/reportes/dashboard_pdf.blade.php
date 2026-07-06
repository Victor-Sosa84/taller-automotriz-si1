<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Dashboard — Reporte Gráfico Operativo</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #222;
            margin: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #f29436;
            /* Color naranja institucional */
            padding-bottom: 12px;
            margin-bottom: 25px;
        }

        .title {
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            color: #111;
        }

        .subtitle {
            font-size: 13px;
            color: #555;
            margin-top: 5px;
        }

        .filtro-info {
            font-size: 14px;
            margin-bottom: 20px;
            color: #444;
            background: #f8f9fa;
            padding: 8px 12px;
            border-left: 4px solid #36a2eb;
        }

        /* --- ESTILOS DE LA GRÁFICA DE BARRAS EN CSS --- */
        .chart-container {
            margin: 30px 0;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 20px;
            background: #fafafa;
        }

        .chart-title {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 20px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .bar-group {
            margin-bottom: 15px;
        }

        .bar-label {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 4px;
            color: #444;
        }

        .bar-wrapper {
            background: #e9ecef;
            border-radius: 4px;
            width: 100%;
            height: 24px;
        }

        .bar-fill {
            height: 100%;
            border-radius: 4px;
            text-align: right;
            padding-right: 10px;
            line-height: 24px;
            color: #fff;
            font-size: 11px;
            font-weight: bold;
        }

        /* Colores dinámicos correspondientes a Chart.js */
        .bg-ordenes {
            background-color: #f29436;
        }

        .bg-ingresos {
            background-color: #36a2eb;
        }

        .bg-repuestos {
            background-color: #ff6384;
        }

        .bg-mecanicos {
            background-color: #4bc1c2;
        }

        /* --- TABLA INFERIOR --- */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        th,
        td {
            border: 1px solid #dee2e6;
            padding: 10px 12px;
            font-size: 12px;
            text-align: left;
        }

        th {
            background-color: #f1f3f5;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
            color: #495057;
        }

        .footer {
            margin-top: 40px;
            font-size: 10px;
            color: #888;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="title">Sistema Web Taller JECOES-Tronic</div>
        <div class="subtitle">Reporte de Rendimiento Operativo Diario (CU23)</div>
    </div>

    <div class="filtro-info">
        <strong>Fecha de Filtro Evaluada:</strong> {{ \Carbon\Carbon::parse($metricas['fecha_filtro'])->format('d/m/Y')
        }}
    </div>

    {{-- 📊 SECCIÓN GRÁFICA (+grafico) --}}
    <div class="chart-container">
        <div class="chart-title">📊 Representación Gráfica de Indicadores</div>

        @php
        $maximo = max([$metricas['totalOrdenes'], $metricas['ingresosCuotas'], $metricas['totalRepuestosUsados'],
        $metricas['mecanicosActivos'], 10]);

        $pOrdenes = ($metricas['totalOrdenes'] / $maximo) * 100;
        $pIngresos = ($metricas['ingresosCuotas'] / $maximo) * 100;
        $pRepuestos = ($metricas['totalRepuestosUsados'] / $maximo) * 100;
        $pMecanicos = ($metricas['mecanicosActivos'] / $maximo) * 100;
        @endphp

        <div class="bar-group">
            <div class="bar-label">Órdenes de Trabajo (Cant: {{ $metricas['totalOrdenes'] }})</div>
            <div class="bar-wrapper">
                <div class="bar-fill bg-ordenes" style="width: {{ max($pOrdenes, 8) }}%;">{{ $metricas['totalOrdenes']
                    }}</div>
            </div>
        </div>

        <div class="bar-group">
            <div class="bar-label">Ingresos por Cuotas (Monto: ${{ number_format($metricas['ingresosCuotas'], 2) }})
            </div>
            <div class="bar-wrapper">
                <div class="bar-fill bg-ingresos" style="width: {{ max($pIngresos, 8) }}%;">{{
                    $metricas['ingresosCuotas'] > 0 ? '$'.number_format($metricas['ingresosCuotas'],0) : '0' }}</div>
            </div>
        </div>

        <div class="bar-group">
            <div class="bar-label">Repuestos Utilizados (Cant: {{ $metricas['totalRepuestosUsados'] }})</div>
            <div class="bar-wrapper">
                <div class="bar-fill bg-repuestos" style="width: {{ max($pRepuestos, 8) }}%;">{{
                    $metricas['totalRepuestosUsados'] }}</div>
            </div>
        </div>

        <div class="bar-group">
            <div class="bar-label">Mecánicos Asignados / Activos (Cant: {{ $metricas['mecanicosActivos'] }})</div>
            <div class="bar-wrapper">
                <div class="bar-fill bg-mecanicos" style="width: {{ max($pMecanicos, 8) }}%;">{{
                    $metricas['mecanicosActivos'] }}</div>
            </div>
        </div>
    </div>

    {{-- 📋 CUADRÍCULA DE RESPALDO --}}
    <table>
        <thead>
            <tr>
                <th>Indicador del Diagrama de Clases</th>
                <th>Métrica Resumen</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Órdenes de Trabajo</strong></td>
                <td>{{ $metricas['totalOrdenes'] }} registros generados</td>
            </tr>
            <tr>
                <td><strong>Ingresos en Caja (Cuotas)</strong></td>
                <td>${{ number_format($metricas['ingresosCuotas'], 2) }} COP/USD</td>
            </tr>
            <tr>
                <td><strong>Detalle de Repuestos</strong></td>
                <td>{{ $metricas['totalRepuestosUsados'] }} unidades cargadas</td>
            </tr>
            <tr>
                <td><strong>Flujo Operativo (Realiza)</strong></td>
                <td>{{ $metricas['mecanicosActivos'] }} mecánicos asignados</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Documento de Control Interno — Generado el {{ now()->format('d/m/Y H:i') }} para JECOES-Tronic.
    </div>

</body>

</html>