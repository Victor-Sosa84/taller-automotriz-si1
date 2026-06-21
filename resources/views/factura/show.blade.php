@extends('layouts.app')
@section('title', 'Factura #' . $factura->nro)

@section('content')
<div style="max-width:900px;">
    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('orden_trabajo.show', $factura->ordenTrabajo->nro) }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">← Volver a la orden</a>
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; margin-top:.5rem;">
            Factura #{{ $factura->nro }}
        </h2>
        <p style="color:var(--muted); font-size:.95rem; margin-top:.25rem;">
            Emitida el {{ $factura->fecha_emision->format('d/m/Y H:i') }}
        </p>
    </div>

    {{-- Datos de facturación --}}
    <div class="form-card" style="margin-bottom:1.5rem;">
        <div class="form-grid">
            <div class="field-group">
                <label>Nombre / Razón Social</label>
                <p style="margin:0; font-weight:600;">{{ $factura->nombre }}</p>
            </div>
            <div class="field-group">
                <label>NIT</label>
                <p style="margin:0;">{{ $factura->nit }}</p>
            </div>
        </div>
    </div>

    {{-- Vehículo --}}
    <div class="form-card" style="margin-bottom:1.5rem;">
        <div class="form-grid">
            <div class="field-group">
                <label>Placa</label>
                <p style="margin:0; font-weight:600;">{{ $factura->ordenTrabajo->auto->placa ?? '—' }}</p>
            </div>
            <div class="field-group">
                <label>Marca</label>
                <p style="margin:0;">{{ $factura->ordenTrabajo->auto->marca ?? '—' }}</p>
            </div>
            <div class="field-group">
                <label>Modelo</label>
                <p style="margin:0;">{{ $factura->ordenTrabajo->auto->modelo ?? '—' }}</p>
            </div>
            <div class="field-group">
                <label>Tipo</label>
                <p style="margin:0;">{{ $factura->ordenTrabajo->auto->tipo ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- Detalle --}}
    <div class="table-wrap" style="margin-bottom:1.5rem;">
        <div style="padding:.75rem 1rem; background:var(--surface2); border-bottom:1px solid var(--border);">
            <span style="font-family:'Barlow Condensed',sans-serif; font-size:.85rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--muted);">Detalle de factura</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Precio Unit.</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($factura->detalles as $d)
                <tr>
                    <td>{{ $d->descripcion }}</td>
                    <td>{{ $d->tipo }}</td>
                    <td>{{ $d->cantidad }}</td>
                    <td>Bs. {{ number_format($d->precio_unitario, 2) }}</td>
                    <td>Bs. {{ number_format($d->precio, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="form-card" style="margin-bottom:1.5rem; text-align:right;">
        <span style="font-family:'Barlow Condensed',sans-serif; font-size:1.2rem; font-weight:700;">
            Total: Bs. {{ number_format($factura->total, 2) }}
        </span>
    </div>

    <div class="form-actions">
        <a href="{{ route('factura.pdf', $factura->nro) }}" class="btn btn-primary" target="_blank">Descargar PDF</a>
    </div>
</div>
@endsection