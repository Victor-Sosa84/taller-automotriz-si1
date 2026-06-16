@extends('layouts.app')
@section('title', 'Órdenes de Trabajo')

@section('content')
<div style="max-width:1000px;">
    <div style="margin-bottom:1.5rem;">
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; margin-top:.5rem;">Órdenes de Trabajo</h2>
        <p style="color:var(--muted); font-size:.95rem; margin-top:.25rem;">Listado de órdenes de trabajo registradas en el sistema.</p>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr style="vertical-align:middle;">
                    <th>#</th>
                    <th>Placa</th>
                    <th>Proforma</th>
                    <th>Fecha Inicio</th>
                    <th>Estado</th>
                    <th style="text-align:center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ordenes as $orden)
                <tr style="vertical-align:middle;">
                    <td>{{ $orden->nro }}</td>
                    <td>{{ $orden->auto->placa ?? '—' }}</td>
                    <td>{{ $orden->nro_proforma ?? '—' }}</td>
                    <td>{{ $orden->fecha_inicio ? $orden->fecha_inicio->format('d/m/Y') : '—' }}</td>
                    <td>
                        @php
                            $badgeStyle = match($orden->estado) {
                                'En Proceso'               => 'background:rgba(52,152,219,.12);color:#5dade2;border:1px solid rgba(52,152,219,.25);',
                                'Finalizada'               => 'background:rgba(46,204,113,.12);color:var(--success);border:1px solid rgba(46,204,113,.25);',
                                'Anulada'                  => 'background:rgba(231,76,60,.12);color:var(--danger);border:1px solid rgba(231,76,60,.25);',
                                default                    => 'background:rgba(107,117,145,.12);color:var(--muted);border:1px solid rgba(107,117,145,.25);',
                            };
                        @endphp
                        <span class="badge" style="{{ $badgeStyle }}">{{ $orden->estado }}</span>
                    </td>
                    <td style="text-align:center; white-space:nowrap;">
                        @if(auth()->user()->puede('CU14_BUS'))
                        <a href="{{ route('orden_trabajo.show', $orden->nro) }}" class="btn btn-sm btn-ghost">Ver</a>
                        @endif
                        @if(auth()->user()->puede('CU14_MOD'))
                        <a href="{{ route('orden_trabajo.edit', $orden->nro) }}" class="btn btn-sm btn-primary">Editar</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; color:var(--muted); padding:2rem;">No hay órdenes de trabajo registradas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection