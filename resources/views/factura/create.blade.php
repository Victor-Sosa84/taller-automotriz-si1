@extends('layouts.app')
@section('title', 'Generar Factura — OT #' . $orden->nro)

@section('content')
<div style="max-width:900px;">
    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('orden_trabajo.show', $orden->nro) }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">← Volver a la orden</a>
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; margin-top:.5rem;">
            Generar Factura Final — OT #{{ $orden->nro }}
        </h2>
        <p style="color:var(--muted); font-size:.95rem; margin-top:.25rem;">Revise el detalle y confirme los datos de facturación.</p>
    </div>

    @if($errors->any())
        <div class="form-errors">
            <strong>Por favor corrige los siguientes campos:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('factura.store', $orden->nro) }}" method="POST">
        @csrf

        {{-- Datos de facturación --}}
        <div class="form-card" style="margin-bottom:1.5rem;">
            <div style="margin-bottom:1.25rem;">
                <span style="font-family:'Barlow Condensed',sans-serif; font-size:1rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em;">Datos de Facturación</span>
            </div>
            <div class="form-grid">
                <div class="field-group">
                    <label for="nombre">Nombre / Razón Social <span class="req">*</span></label>
                    <input id="nombre" name="nombre" type="text" value="{{ old('nombre', $cliente->nombre) }}" required />
                </div>
                <div class="field-group">
                    <label for="nit">NIT <span class="req">*</span></label>
                    <input id="nit" name="nit" type="text" value="{{ old('nit', $cliente->nit) }}" required />
                    <small style="color:var(--muted); font-size:.8rem;">Para consumidor final, use 99001.</small>
                </div>
            </div>
        </div>

        {{-- Vehículo --}}
        <div class="form-card" style="margin-bottom:1.5rem;">
            <div class="form-grid">
                <div class="field-group">
                    <label>Placa</label>
                    <p style="margin:0; font-weight:600;">{{ $orden->auto->placa ?? '—' }}</p>
                </div>
                <div class="field-group">
                    <label>Marca</label>
                    <p style="margin:0;">{{ $orden->auto->marca ?? '—' }}</p>
                </div>
                <div class="field-group">
                    <label>Modelo</label>
                    <p style="margin:0;">{{ $orden->auto->modelo ?? '—' }}</p>
                </div>
                <div class="field-group">
                    <label>Tipo</label>
                    <p style="margin:0;">{{ $orden->auto->tipo ?? '—' }}</p>
                </div>
            </div>
        </div>

        {{-- Repuestos --}}
        <div class="table-wrap" style="margin-bottom:1.5rem;">
            <div style="padding:.75rem 1rem; background:var(--surface2); border-bottom:1px solid var(--border);">
                <span style="font-family:'Barlow Condensed',sans-serif; font-size:.85rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--muted);">Repuestos utilizados</span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Repuesto</th>
                        <th>Cantidad</th>
                        <th>Precio Unit.</th>
                        <th>Descuento</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orden->detallesRepuesto as $dr)
                    <tr>
                        <td>{{ $dr->repuesto->nombre ?? '—' }}</td>
                        <td>{{ $dr->cantidad }}</td>
                        <td>Bs. {{ number_format($dr->precio_unitario, 2) }}</td>
                        <td>{{ $dr->descuento }}%</td>
                        <td>Bs. {{ number_format($dr->cantidad * $dr->precio_unitario * (1 - $dr->descuento / 100), 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center; color:var(--muted); padding:1rem;">Sin repuestos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mano de Obra --}}
        <div class="table-wrap" style="margin-bottom:1.5rem;">
            <div style="padding:.75rem 1rem; background:var(--surface2); border-bottom:1px solid var(--border);">
                <span style="font-family:'Barlow Condensed',sans-serif; font-size:.85rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--muted);">Mano de obra ejecutada</span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Servicio</th>
                        <th>Cantidad</th>
                        <th>Costo</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orden->detallesTrabajo as $dt)
                    <tr>
                        <td>{{ $dt->manoObra->descripcion ?? '—' }}</td>
                        <td>{{ $dt->cantidad }}</td>
                        <td>Bs. {{ number_format($dt->costo, 2) }}</td>
                        <td>Bs. {{ number_format($dt->cantidad * $dt->costo, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center; color:var(--muted); padding:1rem;">Sin mano de obra.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="form-card" style="margin-bottom:1.5rem; text-align:right;">
            <span style="font-family:'Barlow Condensed',sans-serif; font-size:1.2rem; font-weight:700;">
                Total Real: Bs. {{ number_format($orden->total_real, 2) }}
            </span>
        </div>

        <div class="form-actions">
            <a href="{{ route('orden_trabajo.show', $orden->nro) }}" class="btn btn-ghost" style="color:var(--muted);">Cancelar</a>
            <button type="submit" class="btn btn-primary">Confirmar Facturación</button>
        </div>
    </form>
</div>
@endsection