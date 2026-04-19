@extends('layouts.app')
@section('title', 'Dashboard — Recepcionista')

@section('content')
<div class="card">
    <div class="card-body" style="text-align:center; padding: 3rem;">
        <div style="font-size:3rem; margin-bottom:1rem;">📋</div>
        <div style="font-family:'Barlow Condensed',sans-serif; font-size:1.5rem; font-weight:800; text-transform:uppercase; margin-bottom:.5rem;">
            Bienvenido, {{ auth()->user()->nombre_usuario }}
        </div>
        <div style="color:var(--muted); font-size:.9rem;">
            Tu módulo de proformas y clientes estará disponible próximamente.
        </div>
    </div>
</div>
@endsection
