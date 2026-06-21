<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a2e; background: #fff; font-weight: normal; }

        .section { padding: 16px 32px; }
        .section-title { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #888; margin-bottom: 8px; padding-bottom: 4px; border-bottom: 1px solid #e8eaf0; }

        table.detalle { width: 100%; border-collapse: collapse; font-size: 10px; }
        table.detalle thead tr { background: #f5a623; color: #fff; }
        table.detalle thead th { padding: 6px 8px; text-align: left; font-weight: 700; text-transform: uppercase; font-size: 9px; letter-spacing: .06em; }
        table.detalle thead th.text-right { text-align: right; }
        table.detalle thead th.text-center { text-align: center; }
        table.detalle tbody tr { border-bottom: 1px solid #f0f0f0; }
        table.detalle tbody tr:nth-child(even) { background: #fafafa; }
        table.detalle tbody td { padding: 6px 8px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

    {{-- Header --}}
    <table style="width:100%; padding:24px 32px 16px; border-bottom:3px solid #f5a623;">
        <tr>
            <td>
                <div style="font-size:22px; font-weight:700; text-transform:uppercase; letter-spacing:.04em;">
                    ⚙ Taller <span style="color:#f5a623;">Automotriz</span>
                </div>
                <div style="font-size:9px; color:#888; text-transform:uppercase; letter-spacing:.1em; margin-top:2px;">
                    JECOES Tronic — Sistema de Gestión
                </div>
            </td>
            <td style="text-align:right;">
                <div style="font-size:20px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#f5a623;">Factura</div>
                <div style="font-size:13px; color:#555; margin-top:2px;">Factura #{{ $factura->nro }}</div>
            </td>
        </tr>
    </table>

    {{-- Meta — Cliente --}}
    <table style="width:100%; padding: 16px 32px 8px; background:#f8f9fc;">
        <tr>
            <td style="padding:8px 16px;">
                <div style="font-size:8px; text-transform:uppercase; letter-spacing:.1em; color:#888; margin-bottom:3px; font-weight:400;">Fecha de emisión</div>
                <div style="font-size:12px; font-weight:700; color:#1a1a2e;">{{ $factura->fecha_emision->format('d/m/Y H:i') }}</div>
            </td>
            <td style="padding:8px 16px;">
                <div style="font-size:8px; text-transform:uppercase; letter-spacing:.1em; color:#888; margin-bottom:3px; font-weight:400;">Cliente</div>
                <div style="font-size:12px; font-weight:700; color:#1a1a2e;">{{ $factura->nombre }}</div>
            </td>
            <td style="padding:8px 16px;">
                <div style="font-size:8px; text-transform:uppercase; letter-spacing:.1em; color:#888; margin-bottom:3px; font-weight:400;">NIT</div>
                <div style="font-size:12px; font-weight:700; color:#1a1a2e;">{{ $factura->nit }}</div>
            </td>
        </tr>
    </table>

    {{-- Meta — Vehículo --}}
    <table style="width:100%; padding: 0 32px 16px; background:#f8f9fc; border-bottom:1px solid #e8eaf0;">
        <tr>
            <td style="padding:8px 16px;">
                <div style="font-size:8px; text-transform:uppercase; letter-spacing:.1em; color:#888; margin-bottom:3px; font-weight:400;">Vehículo</div>
                <div style="font-size:12px; font-weight:700; color:#f5a623;">{{ $factura->ordenTrabajo->auto->placa ?? '—' }}</div>
            </td>
            <td style="padding:8px 16px;">
                <div style="font-size:8px; text-transform:uppercase; letter-spacing:.1em; color:#888; margin-bottom:3px; font-weight:400;">Marca</div>
                <div style="font-size:12px; font-weight:700; color:#1a1a2e;">{{ $factura->ordenTrabajo->auto->marca ?? '—' }}</div>
            </td>
            <td style="padding:8px 16px;">
                <div style="font-size:8px; text-transform:uppercase; letter-spacing:.1em; color:#888; margin-bottom:3px; font-weight:400;">Modelo</div>
                <div style="font-size:12px; font-weight:700; color:#1a1a2e;">{{ $factura->ordenTrabajo->auto->modelo ?? '—' }}</div>
            </td>
            <td style="padding:8px 16px;">
                <div style="font-size:8px; text-transform:uppercase; letter-spacing:.1em; color:#888; margin-bottom:3px; font-weight:400;">Tipo</div>
                <div style="font-size:12px; font-weight:700; color:#1a1a2e;">{{ $factura->ordenTrabajo->auto->tipo ?? '—' }}</div>
            </td>
        </tr>
    </table>

    {{-- Detalle --}}
    <div class="section">
        <div class="section-title">Detalle de factura</div>
        <table class="detalle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Descripción</th>
                    <th>Tipo</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-right">Precio unit.</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($factura->detalles as $i => $d)
                <tr>
                    <td style="color:#aaa;">{{ $i + 1 }}</td>
                    <td>{{ $d->descripcion }}</td>
                    <td>{{ $d->tipo }}</td>
                    <td class="text-center">{{ $d->cantidad }}</td>
                    <td class="text-right" style="white-space:nowrap;">Bs {{ number_format($d->precio_unitario, 2) }}</td>
                    <td class="text-right" style="font-weight:700; white-space:nowrap;">Bs {{ number_format($d->precio, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Total --}}
    <table style="width:100%; margin:0 0 24px; padding:0 32px;">
        <tr>
            <td style="padding:0 32px;">
                <table style="width:100%; padding:12px 20px; background:#f8f9fc; border:2px solid #f5a623; border-radius:6px;">
                    <tr>
                        <td style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#555;">
                            Total
                        </td>
                        <td style="text-align:right; font-size:16px; font-weight:700; color:#f5a623;">
                            Bs {{ number_format($factura->total, 2) }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Footer --}}
    <table style="width:100%; margin:0; padding:16px 32px 0; border-top:1px solid #e8eaf0;">
        <tr>
            <td style="font-size:9px; color:#aaa;">JECOES Tronic — Documento generado el {{ now()->format('d/m/Y H:i') }}</td>
            <td style="text-align:right; font-size:9px; color:#aaa;">Factura #{{ $factura->nro }}</td>
        </tr>
    </table>

</body>
</html>