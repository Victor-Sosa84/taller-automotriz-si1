<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Acta de Entrega - Orden #{{ $orden->nro }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 14px;
        }

        .section-title {
            font-weight: bold;
            text-transform: uppercase;
            background: #f2f2f2;
            padding: 5px 10px;
            font-size: 14px;
            margin-top: 20px;
            border: 1px solid #ddd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 13px;
            vertical-align: top;
        }

        table td.label {
            font-weight: bold;
            width: 25%;
            background: #fafafa;
        }

        .observaciones {
            border: 1px solid #ddd;
            padding: 15px;
            min-height: 80px;
            font-size: 13px;
            margin-top: 10px;
            background: #fafafa;
        }

        .firmas {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
        }

        .firma-block {
            text-align: center;
            width: 45%;
        }

        .firma-line {
            border-top: 1px solid #000;
            margin-top: 50px;
            padding-top: 5px;
            font-size: 13px;
            font-weight: bold;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                margin: 0;
            }
        }
    </style>
</head>

<body>

    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print();"
            style="padding: 8px 16px; background: #f29436; border: none; font-weight: bold; cursor: pointer;">🖨️
            Imprimir Acta</button>
    </div>

    <div class="header">
        <h1>🛠️ TALLER AUTOMOTRIZ JECOES-Tronic</h1>
        <p>Acta Oficial de Constancia de Entrega y Salida de Vehículo</p>
    </div>

    <table style="margin-bottom: 15px;">
        <tr>
            <td class="label">Nro. Orden:</td>
            <td><strong>#{{ $orden->nro }}</strong></td>
            <td class="label">Fecha de Entrega:</td>
            <td>{{ date('d/m/Y H:i', strtotime($recoge->fecha)) }}</td>
        </tr>
        <tr>
            <td class="label">Placa del Auto:</td>
            <td>{{ $orden->placa_auto }}</td>
            <td class="label">Nro. Proforma:</td>
            <td>{{ $orden->nro_proforma }}</td>
        </tr>
    </table>

    <div class="section-title">👤 Datos del Receptor (Persona que Recoge el Vehículo)</div>
    <table>
        <tr>
            <td class="label">Nombre Completo:</td>
            <td>{{ $persona->nombre }}</td>
        </tr>
        <tr>
            <td class="label">Cédula de Identidad (CI):</td>
            <td>{{ $persona->ci }}</td>
        </tr>
        <tr>
            <td class="label">Relación / Parentesco:</td>
            <td>{{ $recoge->relacion }}</td>
        </tr>
    </table>

    <div class="section-title">📝 Notas y Observaciones de Salida</div>
    <div class="observaciones">
        {{ $orden->observacion_salida ?? 'Sin observaciones particulares registradas al momento del cierre.' }}
    </div>

    <div style="margin-top: 30px; font-size: 11px; color: #555; text-align: justify;">
        Con la firma de la presente acta, el receptor declara haber verificado a satisfacción las reparaciones mecánicas
        efectuadas en el vehículo, así como sus inventarios, accesorios e integridad general al momento del retiro de
        los talleres.
    </div>

    <div class="firmas">
        <div class="firma-block">
            <div class="firma-line">Firma del Receptor<br>CI: {{ $persona->ci }}</div>
        </div>
        <div class="firma-block">
            <div class="firma-line">Por JECOES-Tronic<br>Firma y Sello Autorizado</div>
        </div>
    </div>

    <script>
        // Dispara automáticamente el cuadro de impresión al cargar la pestaña
        window.onload = function() { window.print(); }
    </script>
</body>

</html>