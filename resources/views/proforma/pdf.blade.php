<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a2e; background: #fff; font-weight: normal; }

        .header { padding: 24px 32px 16px; border-bottom: 3px solid #f5a623; display: flex; justify-content: space-between; align-items: flex-start; }
        .brand-name { font-size: 22px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
        .brand-name span { color: #f5a623; }
        .brand-sub { font-size: 9px; color: #888; text-transform: uppercase; letter-spacing: .1em; margin-top: 2px; }
        .doc-title { text-align: right; }
        .doc-title h1 { font-size: 20px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #f5a623; }
        .doc-title .doc-nro { font-size: 13px; color: #555; margin-top: 2px; }

        .meta { padding: 16px 32px; display: flex; justify-content: space-between; background: #f8f9fc; border-bottom: 1px solid #e8eaf0; }
        .meta-block { }
        .meta-label { font-size: 8px; text-transform: uppercase; letter-spacing: .1em; color: #888; margin-bottom: 3px; }
        .meta-value { font-size: 11px; font-weight: 600; }
        .meta-value.accent { color: #f5a623; font-size: 13px; }

        .estado-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; }
        .estado-Borrador  { background: #eee; color: #666; }
        .estado-Emitida   { background: #dbeafe; color: #1d4ed8; }
        .estado-Aprobada  { background: #dcfce7; color: #166534; }
        .estado-Observada { background: #fef9c3; color: #854d0e; }
        .estado-Anulada   { background: #fee2e2; color: #991b1b; }

        .section { padding: 16px 32px; }
        .section-title { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #888; margin-bottom: 8px; padding-bottom: 4px; border-bottom: 1px solid #e8eaf0; }

        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        thead tr { background: #f5a623; color: #fff; }
        thead th { padding: 6px 8px; text-align: left; font-weight: 700; text-transform: uppercase; font-size: 9px; letter-spacing: .06em; }
        tbody tr { border-bottom: 1px solid #f0f0f0; }
        tbody tr:last-child { border-bottom: none; }
        tbody td { padding: 6px 8px; }
        tbody tr:nth-child(even) { background: #fafafa; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .total-box { margin: 0 32px 24px; padding: 12px 20px; background: #f8f9fc; border: 2px solid #f5a623; border-radius: 6px; display: flex; justify-content: space-between; align-items: center; }
        .total-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #555; }
        .total-value { font-size: 22px; font-weight: 700; color: #f5a623; }

        .footer { margin: 0 32px; padding: 16px 0 0; border-top: 1px solid #e8eaf0; display: flex; justify-content: space-between; font-size: 9px; color: #aaa; }

        .empty-row td { color: #aaa; font-style: italic; text-align: center; padding: 12px; }
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
                <div style="font-size:20px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#f5a623;">Cotización</div>
                <div style="font-size:13px; color:#555; margin-top:2px;">Proforma #{{ $proforma->nro }}</div>
                <div style="margin-top:4px;">
                    <span class="estado-badge estado-{{ $proforma->estado }}">{{ $proforma->estado }}</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- Meta --}}
    <table style="width:100%; padding: 16px 32px; background:#f8f9fc; border-bottom:1px solid #e8eaf0;">
        <tr>
            <td style="padding:8px 16px;">
                <div style="font-size:8px; text-transform:uppercase; letter-spacing:.1em; color:#888; margin-bottom:3px; font-weight:400;">Fecha de emisión</div>
                <div style="font-size:12px; font-weight:700; color:#1a1a2e;">{{ $proforma->fecha?->format('d/m/Y H:i') }}</div>
            </td>
            <td style="padding:8px 16px;">
                <div style="font-size:8px; text-transform:uppercase; letter-spacing:.1em; color:#888; margin-bottom:3px; font-weight:400;">Vehículo</div>
                <div style="font-size:12px; font-weight:700; color:#f5a623;">{{ $proforma->diagnostico->auto->placa ?? '—' }}</div>
            </td>
            <td style="padding:8px 16px;">
                <div style="font-size:8px; text-transform:uppercase; letter-spacing:.1em; color:#888; margin-bottom:3px; font-weight:400;">Marca / Modelo</div>
                <div style="font-size:12px; font-weight:700; color:#1a1a2e;">{{ $proforma->diagnostico->auto->marca ?? '—' }} {{ $proforma->diagnostico->auto->modelo ?? '' }}</div>
            </td>
            <td style="padding:8px 16px;">
                <div style="font-size:8px; text-transform:uppercase; letter-spacing:.1em; color:#888; margin-bottom:3px; font-weight:400;">CI Cliente</div>
                <div style="font-size:12px; font-weight:700; color:#1a1a2e;">{{ $proforma->ci_cliente }}</div>
            </td>
            <td style="padding:8px 16px;">
                <div style="font-size:8px; text-transform:uppercase; letter-spacing:.1em; color:#888; margin-bottom:3px; font-weight:400;">Plazo estimado</div>
                <div style="font-size:12px; font-weight:700; color:#1a1a2e;">{{ $proforma->plazo ? \Carbon\Carbon::parse($proforma->plazo)->format('d/m/Y') : '—' }}</div>
            </td>
        </tr>
    </table>

    {{-- Repuestos --}}
    <div class="section">
        <div class="section-title">Repuestos</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Repuesto</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-right">Precio unit.</th>
                    <th class="text-right">Descuento</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proforma->repuestos as $i => $r)
                    @php $sub = ($r->precio_unitario * $r->cantidad) * (1 - $r->descuento / 100); @endphp
                    <tr>
                        <td style="color:#aaa;">{{ $i + 1 }}</td>
                        <td>{{ $r->repuesto->nombre ?? '—' }}
                            @if($r->repuesto->marca) <span style="color:#aaa;">— {{ $r->repuesto->marca }}</span> @endif
                        </td>
                        <td class="text-center">{{ $r->cantidad }}</td>
                        <td class="text-right" style="white-space:nowrap;">Bs {{ number_format($r->precio_unitario, 2) }}</td>
                        <td class="text-right" style="white-space:nowrap;">{{ $r->descuento }}%</td>
                        <td class="text-right" style="font-weight:700; white-space:nowrap;">Bs {{ number_format($sub, 2) }}</td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="6">Sin repuestos</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Servicios --}}
    <div class="section">
        <div class="section-title">Servicios / Mano de Obra</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Servicio</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-right">Costo unit.</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proforma->servicios as $i => $s)
                    @php $sub = $s->costo * $s->cantidad; @endphp
                    <tr>
                        <td style="color:#aaa;">{{ $i + 1 }}</td>
                        <td>{{ $s->manoObra->descripcion ?? '—' }}</td>
                        <td class="text-center">{{ $s->cantidad }}</td>
                        <td class="text-right" style="white-space:nowrap;">Bs {{ number_format($s->costo, 2) }}</td>
                        <td class="text-right" style="font-weight:700; white-space:nowrap;">Bs {{ number_format($sub, 2) }}</td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="5">Sin servicios</td></tr>
                @endforelse
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
                            Total aproximado
                        </td>
                        <td style="text-align:right; font-size:22px; font-weight:700; color:#f5a623;">
                            Bs {{ number_format($proforma->total_aprox, 2) }}
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
            <td style="text-align:right; font-size:9px; color:#aaa;">Proforma #{{ $proforma->nro }} — {{ $proforma->estado }}</td>
        </tr>
    </table>

</body>
</html>