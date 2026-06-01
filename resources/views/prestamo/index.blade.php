@extends('layouts.app')
@section('title', 'Préstamos de Herramientas')

@section('content')
<div style="max-width:960px;">
    <div style="margin-bottom:1.5rem;">
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; margin-top:.5rem;">Préstamos de Herramientas</h2>
        <p style="color:var(--muted); font-size:.95rem; margin-top:.25rem;">Registro y seguimiento de préstamos de herramientas del taller.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    {{-- Formulario nuevo préstamo --}}
    @if(auth()->user()->puede('CU09_ADD'))
    <div class="form-card" style="margin-bottom:1.5rem;">
        <div style="margin-bottom:1.25rem;">
            <span style="font-family:'Barlow Condensed',sans-serif; font-size:1rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em;">Registrar Préstamo</span>
        </div>
        <form action="{{ route('prestamo.store') }}" method="POST">
            @csrf
            <div class="form-grid">
                <div class="field-group">
                    <label for="nro_herramienta">Herramienta <span class="req">*</span></label>
                    <select id="nro_herramienta" name="nro_herramienta" required>
                        <option value="">Seleccionar...</option>
                        @foreach($herramientas as $h)
                        <option value="{{ $h->nro }}">{{ $h->descripcion }} — {{ $h->tipo->descripcion ?? '' }} / {{ $h->marca->nombre ?? '' }}</option>
                        @endforeach
                    </select>
                    @if($herramientas->isEmpty())
                        <span style="font-size:.75rem; color:var(--danger);">No hay herramientas disponibles.</span>
                    @endif
                </div>
                <div class="field-group">
                    <label for="fecha_salida">Fecha de Salida <span class="req">*</span></label>
                    <input id="fecha_salida" name="fecha_salida" type="datetime-local"
                        value="{{ now()->format('Y-m-d\TH:i') }}" required />
                </div>
                <div class="field-group" style="grid-column:1 / -1;">
                    <label for="estado_salida">Estado de Salida</label>
                    <select id="estado_salida" name="estado_salida">
                        <option value="Bueno">Bueno</option>
                        <option value="Regular">Regular</option>
                        <option value="Malo">Malo</option>
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Registrar Préstamo</button>
            </div>
        </form>
    </div>
    @endif

    {{-- Lista de préstamos --}}
    <div class="table-wrap">
        <div style="padding:.75rem 1rem; background:var(--surface2); border-bottom:1px solid var(--border);">
            <span style="font-family:'Barlow Condensed',sans-serif; font-size:.85rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--muted);">Préstamos registrados</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Herramienta</th>
                    <th>Estado Salida</th>
                    <th>Fecha Salida</th>
                    <th>Fecha Devolución</th>
                    <th>Estado Retorno</th>
                </tr>
            </thead>
            <tbody>
                @forelse($prestamos as $p)
                    @foreach($p->detalles as $d)
                    <tr style="vertical-align:middle;">
                        <td class="td-muted">{{ $p->id }}</td>
                        <td>{{ $d->herramienta->descripcion ?? '—' }}</td>
                        <td>{{ $d->estado_salida ?? '—' }}</td>
                        <td>{{ $p->fecha_salida->format('d/m/Y H:i') }}</td>
                        <td>{{ $p->fecha_devolucion ? $p->fecha_devolucion->format('d/m/Y H:i') : '—' }}</td>
                        <td>
                            @if($d->estado_retorno)
                                <span style="color:var(--success);">{{ $d->estado_retorno }}</span>
                            @else
                                <span style="color:var(--accent);">Pendiente</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; color:var(--muted); padding:2rem;">No hay préstamos registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection