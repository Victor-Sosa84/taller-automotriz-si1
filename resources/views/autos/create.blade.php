@extends('layouts.app')
@section('title', 'Nuevo Vehículo')

@section('content')
<div style="max-width:700px;">
    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('autos.index') }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">
            ← Volver a vehículos
        </a>
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; text-transform:uppercase; margin-top:.5rem;">
            Nuevo Vehículo
        </h2>
    </div>

    @include('autos._form', [
        'action' => route('autos.store'),
        'method' => 'POST',
        'auto'   => null,
    ])
</div>
@endsection
