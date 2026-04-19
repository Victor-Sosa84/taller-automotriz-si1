<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Taller Automotriz</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Barlow', system-ui, sans-serif;
            background: url('{{ asset("images/login-bg.jpg") }}') center/cover no-repeat fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(10, 12, 20, 0.75);
            z-index: 0;
            pointer-events: none;
        }

        .login-box {
            background: #181c27;
            border: 1px solid #2a3045;
            border-top: 3px solid #f5a623;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 390px;
            position: relative;
            z-index: 1;
        }

        /* ── Cabecera — esencia original conservada ── */
        .workshop-header {
            text-align: center;
            margin-bottom: 1.75rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid #2a3045;
        }

        .workshop-name {
            display: block;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 1.35rem;
            font-weight: 800;
            color: #e8eaf0;
            letter-spacing: .04em;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .workshop-name span { color: #f5a623; }

        .workshop-subtitle {
            display: block;
            font-size: 0.78rem;
            color: #6b7591;
            margin-top: 0.35rem;
            font-weight: 500;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        h2 {
            text-align: center;
            color: #e8eaf0;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        /* ── Form ── */
        .form-group { margin-bottom: 1.2rem; }

        label {
            display: block;
            margin-bottom: 0.4rem;
            color: #6b7591;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: .07em;
            text-transform: uppercase;
        }

        input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid #2a3045;
            border-radius: 8px;
            font-size: 0.95rem;
            font-family: 'Barlow', sans-serif;
            transition: all 0.2s ease;
            background: #1f2436;
            color: #e8eaf0;
        }

        input::placeholder { color: #4a5270; }

        input:focus {
            outline: none;
            border-color: #f5a623;
            box-shadow: 0 0 0 3px rgba(245, 166, 35, 0.12);
            background: #232840;
        }

        /* ── Hint debajo del campo ── */
        .field-hint {
            font-size: 0.72rem;
            color: #4a5270;
            margin-top: 0.3rem;
        }

        /* ── Error ── */
        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            color: #f87171;
            padding: 0.65rem 0.9rem;
            border-radius: 7px;
            font-size: 0.85rem;
            text-align: center;
            margin-bottom: 1.2rem;
            border: 1px solid rgba(239, 68, 68, 0.25);
        }

        /* ── Botón — misma esencia, nuevo color ── */
        button[type="submit"] {
            width: 100%;
            padding: 0.85rem;
            background: #f5a623;
            color: #111;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-family: 'Barlow', sans-serif;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.1s ease;
            margin-top: 0.5rem;
            letter-spacing: .03em;
        }

        button[type="submit"]:hover  { background: #ffc04a; }
        button[type="submit"]:active { transform: scale(0.98); }

        /* ── Link olvidé contraseña ── */
        .forgot-link {
            text-align: right;
            margin-top: 0.5rem;
        }

        .forgot-link a {
            font-size: 0.8rem;
            color: #6b7591;
            text-decoration: none;
            transition: color .15s;
        }

        .forgot-link a:hover { color: #f5a623; }
    </style>
</head>
<body>

    <div class="login-box">

        {{-- Cabecera — esencia original conservada --}}
        <div class="workshop-header">
            <span class="workshop-name">Taller <span>Automotriz</span></span>
            <span class="workshop-subtitle">Sistema de Acceso Interno</span>
        </div>

        <h2>Bienvenido</h2>

        @if($errors->any())
            <div class="error-msg">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="correo">Correo o usuario</label>
                <input type="text"
                       id="correo"
                       name="correo"
                       value="{{ old('correo') }}"
                       placeholder="correo@taller.com  o  mprueba"
                       required
                       autofocus>
                <div class="field-hint">Puedes ingresar tu correo electrónico o tu nombre de usuario.</div>
            </div>

            <div class="form-group">
                <label for="clave">Contraseña</label>
                <input type="password"
                       id="clave"
                       name="clave"
                       placeholder="••••••••"
                       required>
            </div>

            @if (Route::has('password.request'))
                <div class="forgot-link">
                    <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                </div>
            @endif

            <button type="submit">Iniciar Sesión</button>
        </form>

    </div>

</body>
</html>