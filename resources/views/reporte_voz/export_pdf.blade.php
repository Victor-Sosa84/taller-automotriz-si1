<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte — {{ $funcion }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 16px; border-bottom: 2px solid #f5a623; padding-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #eee; }
        .meta { color: #666; font-size: 10px; margin-bottom: 16px; }
    </style>
</head>
<body>
    <h1>Reporte: {{ str_replace(['buscar', 'contar'], '', $funcion) }}</h1>
    <div class="meta">Generado el {{ now()->format('d/m/Y H:i') }} — JECOES Tronic</div>

    @php
        $listaAnidada = null;
        $columnas = [];
        if (is_array($resultado)) {
            foreach ($resultado as $valor) {
                if (is_array($valor) && isset($valor[0]) && is_array($valor[0])) {
                    $listaAnidada = $valor;
                    $columnas = array_keys($valor[0]);
                    break;
                }
            }
        }
    @endphp

    @if($listaAnidada)
        <table>
            <thead>
                <tr>
                    @foreach($columnas as $col)
                        <th>{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($listaAnidada as $fila)
                    <tr>
                        @foreach($columnas as $col)
                            <td>{{ is_array($fila[$col] ?? null) ? implode(', ', $fila[$col]) : ($fila[$col] ?? '—') }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif(is_array($resultado))
        <table>
            <tbody>
                @foreach($resultado as $clave => $valor)
                    <tr>
                        <th>{{ $clave }}</th>
                        <td>{{ is_array($valor) ? implode(', ', $valor) : $valor }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>{{ $resultado }}</p>
    @endif
</body>
</html>
