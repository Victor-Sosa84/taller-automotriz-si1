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
        <strong>Período:</strong> {{ ucfirst($metricas['periodo']) }} &nbsp;|&nbsp;
        <strong>Rango evaluado:</strong> {{ $metricas['rango_inicio'] }} — {{ $metricas['rango_fin'] }}
    </div>

    {{-- 📊 1. INGRESOS (línea, igual que en pantalla) --}}
    <div class="chart-container">
        <div class="chart-title">📈 Evolución de Ingresos ($)</div>
        @if($imgIngresos)
        <img src="{{ $imgIngresos }}" style="width: 100%; max-height: 260px;">
        @else
        @php $maxIngreso = max($ingresos->max('total') ?? 0, 1); @endphp
        @forelse($ingresos as $item)
        <div class="bar-group">
            <div class="bar-label">{{ $item->periodo }} (${{ number_format($item->total, 2) }})</div>
            <div class="bar-wrapper">
                <div class="bar-fill bg-ingresos" style="width: {{ max(($item->total / $maxIngreso) * 100, 8) }}%;">${{
                    number_format($item->total, 0) }}</div>
            </div>
        </div>
        @empty
        <p style="font-size:12px; color:#888;">Sin ingresos registrados en el período.</p>
        @endforelse
        @endif
    </div>

    {{-- 📊 2. ESTADOS DE ÓRDENES (dona, igual que en pantalla) --}}
    <div class="chart-container">
        <div class="chart-title">🍩 Carga de Trabajo (Estados)</div>
        @if($imgEstados)
        <img src="{{ $imgEstados }}" style="width: 100%; max-height: 260px;">
        @else
        @php $maxEstado = max($estados->max('total') ?? 0, 1); @endphp
        @forelse($estados as $item)
        <div class="bar-group">
            <div class="bar-label">{{ $item->estado }} (Cant: {{ $item->total }})</div>
            <div class="bar-wrapper">
                <div class="bar-fill bg-ordenes" style="width: {{ max(($item->total / $maxEstado) * 100, 8) }}%;">{{
                    $item->total }}</div>
            </div>
        </div>
        @empty
        <p style="font-size:12px; color:#888;">Sin órdenes registradas en el período.</p>
        @endforelse
        @endif
    </div>

    {{-- 📊 3. TOP MECÁNICOS (barras horizontales, igual que en pantalla) --}}
    <div class="chart-container">
        <div class="chart-title">👨‍🔧 Top Mecánicos (Trabajos Realizados)</div>
        @if($imgMecanicos)
        <img src="{{ $imgMecanicos }}" style="width: 100%; max-height: 260px;">
        @else
        @php $maxMec = max($mecanicos->max('trabajos_realizados') ?? 0, 1); @endphp
        @forelse($mecanicos as $item)
        <div class="bar-group">
            <div class="bar-label">{{ $item->nombre }} (Cant: {{ $item->trabajos_realizados }})</div>
            <div class="bar-wrapper">
                <div class="bar-fill bg-mecanicos"
                    style="width: {{ max(($item->trabajos_realizados / $maxMec) * 100, 8) }}%;">{{
                    $item->trabajos_realizados }}</div>
            </div>
        </div>
        @empty
        <p style="font-size:12px; color:#888;">Sin mecánicos asignados en el período.</p>
        @endforelse
        @endif
    </div>

    {{-- 📊 4. TOP REPUESTOS (barras verticales, igual que en pantalla) --}}
    <div class="chart-container">
        <div class="chart-title">📦 Top Repuestos de Mayor Rotación</div>
        @if($imgRepuestos)
        <img src="{{ $imgRepuestos }}" style="width: 100%; max-height: 260px;">
        @else
        @php $maxRep = max($repuestos->max('total_usado') ?? 0, 1); @endphp
        @forelse($repuestos as $item)
        <div class="bar-group">
            <div class="bar-label">{{ $item->nombre }} (Cant: {{ $item->total_usado }})</div>
            <div class="bar-wrapper">
                <div class="bar-fill bg-repuestos" style="width: {{ max(($item->total_usado / $maxRep) * 100, 8) }}%;">
                    {{ $item->total_usado }}</div>
            </div>
        </div>
        @empty
        <p style="font-size:12px; color:#888;">Sin repuestos utilizados en el período.</p>
        @endforelse
        @endif
    </div>

    <div class="footer">
        Documento de Control Interno — Generado el {{ now()->format('d/m/Y H:i') }} para JECOES-Tronic.
    </div>

</body>

</html>