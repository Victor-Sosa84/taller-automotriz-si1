<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña — Taller Automotriz</title>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        /* Pega aquí exactamente el mismo CSS que usaste en tu login.blade.php */
        /* Así mantienes la estética del taller, el fondo oscuro y el borde naranja */
    </style>
</head>
<body>

    <div class="login-box">
        <div class="workshop-header">
            <span class="workshop-name">Taller <span>Automotriz</span></span>
            <span class="workshop-subtitle">Recuperación de Acceso</span>
        </div>

        <p style="color: #6b7591; font-size: 0.85rem; text-align: center; margin-bottom: 1.5rem;">
            Ingresa tu correo y te enviaremos un enlace para restablecer tu clave.
        </p>

        @if (session('status'))
            <div style="color: #f5a623; margin-bottom: 1rem; text-align: center;">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-group">
                <label for="correo">Correo Electrónico</label>
                <input type="email" id="correo" name="correo" required autofocus>
            </div>

            <button type="submit">Enviar Enlace</button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem;">
            <a href="{{ route('login') }}" style="color: #6b7591; text-decoration: none; font-size: 0.8rem;">Volver al Inicio</a>
        </div>
    </div>

</body>
</html>