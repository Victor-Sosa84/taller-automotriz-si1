@extends('layouts.app')
@section('title', 'Editar Vehículo')

@section('content')
<div style="max-width:700px;">
    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('autos.index') }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">
            ← Volver a vehículos
        </a>
        <div style="display:flex; align-items:baseline; gap:1rem; margin-top:.5rem;">
            <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; text-transform:uppercase;">
                Editar — {{ $auto->placa }}
            </h2>
            @if($auto->marca && $auto->modelo)
                <span style="font-size:.85rem; color:var(--muted);">
                    {{ $auto->marca }} {{ $auto->modelo }}
                </span>
            @endif
        </div>
    </div>

    @include('autos._form', [
        'action' => route('autos.update', $auto->placa),
        'method' => 'PUT',
        'auto'   => $auto,
    ])
</div>
@endsection
