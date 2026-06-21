@extends('layouts.app')
@section('title', 'Registrar Pago — Factura #' . $factura->nro)

@section('content')
<div style="max-width:760px;">
    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('factura.show', $factura->nro) }}" style="font-size:.8rem; color:var(--muted); text-decoration:none;">← Volver a la factura</a>
        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:800; margin-top:.5rem;">
            Registrar Pago — Factura #{{ $factura->nro }}
        </h2>
        <p style="color:var(--muted); font-size:.95rem; margin-top:.25rem;">Cliente: {{ $factura->nombre }}</p>
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

    {{-- Resumen de saldo --}}
    <div class="form-card" style="margin-bottom:1.5rem;">
        <div class="form-grid">
            <div class="field-group">
                <label>Total Factura</label>
                <p style="margin:0; font-weight:600;">Bs. {{ number_format($factura->total, 2) }}</p>
            </div>
            <div class="field-group">
                <label>Total Abonado</label>
                <p style="margin:0;">Bs. {{ number_format($factura->cuotas->sum('monto'), 2) }}</p>
            </div>
            <div class="field-group">
                <label>Saldo Pendiente</label>
                <p style="margin:0; font-weight:700; color:var(--accent);">Bs. {{ number_format($factura->saldo_pendiente, 2) }}</p>
            </div>
        </div>
    </div>

    {{-- Historial de abonos --}}
    <div class="table-wrap" style="margin-bottom:1.5rem;">
        <div style="padding:.75rem 1rem; background:var(--surface2); border-bottom:1px solid var(--border);">
            <span style="font-family:'Barlow Condensed',sans-serif; font-size:.85rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--muted);">Abonos registrados</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Monto</th>
                    <th>Modalidad</th>
                </tr>
            </thead>
            <tbody>
                @forelse($factura->cuotas as $cuota)
                <tr>
                    <td>{{ $cuota->nro }}</td>
                    <td>{{ $cuota->fecha->format('d/m/Y') }}</td>
                    <td>Bs. {{ number_format($cuota->monto, 2) }}</td>
                    <td>{{ ucfirst($cuota->tipo_pago) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center; color:var(--muted); padding:1.5rem;">Sin abonos registrados aún.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Formulario nuevo abono --}}
    @if($factura->saldo_pendiente > 0)
    <form action="{{ route('cuota.store', $factura->nro) }}" method="POST" class="form-card" id="form-pago">
        @csrf
        <div class="form-grid">
            <div class="field-group">
                <label for="monto">Monto a Abonar <span class="req">*</span></label>
                <input id="monto" name="monto" type="number" step="0.50" min="0.50" max="{{ $factura->saldo_pendiente }}" value="{{ old('monto') }}" required />
                <small id="aviso-monto-bloqueado" style="display:none; color:var(--muted); font-size:.8rem;">
                    El monto quedó fijado para este pago. Para cambiarlo, vuelve a "Efectivo" y selecciona "Tarjeta" nuevamente.
                </small>
            </div>
            <div class="field-group">
                <label for="tipo_pago">Modalidad de Pago <span class="req">*</span></label>
                <select id="tipo_pago" name="tipo_pago" required>
                    <option value="efectivo" {{ old('tipo_pago', 'efectivo') === 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                    <option value="tarjeta" {{ old('tipo_pago') === 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                </select>
                <small id="error-pago" style="display:none; color:#e74c3c; font-size:.8rem;">
                    Ingresa el monto a abonar antes de seleccionar Tarjeta.
                </small>
            </div>
        </div>

        <div id="stripe-elemento" style="display:none; margin-top:1rem; background:var(--surface2); border:1px solid var(--border); border-radius:8px; padding:14px 16px;"></div>

        <div class="form-actions">
            <a href="{{ route('factura.show', $factura->nro) }}" class="btn btn-ghost" style="color:var(--muted);">Cancelar</a>
            <button type="submit" class="btn btn-primary">Registrar Pago</button>
        </div>
    </form>
    @else
    <div class="form-card" style="text-align:center; color:var(--muted);">
        Esta factura ya fue saldada en su totalidad.
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
const stripe = Stripe('{{ config('services.stripe.key') }}');
let elements;
let cardElement;
let clientSecret;

document.getElementById('tipo_pago').addEventListener('change', async function() {
    const montoInput = document.getElementById('monto');
    const avisoMonto = document.getElementById('aviso-monto-bloqueado');
    const errorPago = document.getElementById('error-pago');
    const contenedorStripe = document.getElementById('stripe-elemento');

    if (this.value === 'tarjeta') {
        const monto = parseFloat(montoInput.value);

        if (!monto || monto <= 0) {
            errorPago.textContent = 'Ingresa el monto a abonar antes de seleccionar Tarjeta.';
            errorPago.style.display = 'block';
            this.value = 'efectivo';
            return;
        }

        errorPago.style.display = 'none';
        montoInput.readOnly = true;
        avisoMonto.style.display = 'block';

        const response = await fetch('{{ route('api.cuota.intento_pago') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ monto: monto })
        });
        const data = await response.json();
        clientSecret = data.client_secret;

        elements = stripe.elements({ clientSecret: data.client_secret });
        cardElement = elements.create('card', {
            hidePostalCode: true,
            disableLink: true,
            style: {
                base: {
                    fontSize: '15px',
                    color: '#d8dee9',
                    '::placeholder': { color: '#6b7591' },
                    iconColor: '#d8dee9',
                },
                invalid: { color: '#e74c3c' },
            }
        });
        cardElement.mount('#stripe-elemento');

        contenedorStripe.style.display = 'block';
    } else {
        errorPago.style.display = 'none';
        montoInput.readOnly = false;
        avisoMonto.style.display = 'none';
        contenedorStripe.style.display = 'none';
    }
});

document.getElementById('form-pago').addEventListener('submit', async function(e) {
    const tipoPago = document.getElementById('tipo_pago').value;

    if (tipoPago !== 'tarjeta') {
        return;
    }

    e.preventDefault();

    const errorPago = document.getElementById('error-pago');
    const btnSubmit = this.querySelector('button[type="submit"]');
    btnSubmit.disabled = true;
    btnSubmit.textContent = 'Procesando...';

    const { paymentIntent, error } = await stripe.confirmCardPayment(clientSecret, {
        payment_method: { card: cardElement }
    });

    if (error) {
        errorPago.textContent = error.message;
        errorPago.style.display = 'block';
        btnSubmit.disabled = false;
        btnSubmit.textContent = 'Registrar Pago';
        return;
    }

    const inputReferencia = document.createElement('input');
    inputReferencia.type = 'hidden';
    inputReferencia.name = 'referencia_stripe';
    inputReferencia.value = paymentIntent.id;
    this.appendChild(inputReferencia);

    this.submit();
});
</script>
@endpush