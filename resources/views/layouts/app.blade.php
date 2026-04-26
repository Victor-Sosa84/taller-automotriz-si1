<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Taller Automotriz')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:       #0f1117;
            --surface:  #181c27;
            --surface2: #1f2436;
            --border:   #2a3045;
            --accent:   #f5a623;
            --text:     #e8eaf0;
            --muted:    #6b7591;
            --success:  #2ecc71;
            --danger:   #e74c3c;
            --radius:   6px;
            --sidebar:  220px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Barlow', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; display: flex; }

        /* ── SIDEBAR ── */
        .sidebar {
            width: var(--sidebar);
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
        }
        .sidebar-brand {
            padding: 1.25rem 1.2rem;
            border-bottom: 3px solid var(--accent);
        }
        .sidebar-brand-name {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 1.2rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .sidebar-brand-name span { color: var(--accent); }
        .sidebar-brand-sub { font-size: .7rem; color: var(--muted); letter-spacing: .08em; text-transform: uppercase; margin-top: 2px; }

        .sidebar-user {
            padding: 1rem 1.2rem;
            border-bottom: 1px solid var(--border);
        }
        .sidebar-user-name { font-weight: 600; font-size: .875rem; }
        .sidebar-user-role {
            font-size: .72rem;
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-top: 2px;
        }

        .sidebar-nav { flex: 1; padding: .75rem 0; overflow-y: auto; }
        .nav-section {
            font-size: .65rem;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--muted);
            padding: .75rem 1.2rem .3rem;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: .65rem;
            padding: .6rem 1.2rem;
            font-size: .875rem;
            color: var(--muted);
            text-decoration: none;
            transition: all .12s;
            border-left: 2px solid transparent;
        }
        .nav-item:hover { color: var(--text); background: rgba(255,255,255,.04); }
        .nav-item.active { color: var(--accent); border-left-color: var(--accent); background: rgba(245,166,35,.07); }
        .nav-icon { font-size: 1rem; width: 18px; text-align: center; flex-shrink: 0; }

        .sidebar-footer {
            padding: .75rem 1.2rem;
            border-top: 1px solid var(--border);
        }

        /* ── MAIN ── */
        .main-wrap {
            margin-left: var(--sidebar);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: .9rem 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .topbar-title {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 1.3rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .page-content { padding: 2rem 1.75rem; flex: 1; }

        /* ── ALERTS ── */
        .alert {
            display: flex; align-items: center; gap: .75rem;
            padding: .85rem 1.2rem; border-radius: var(--radius);
            margin-bottom: 1.5rem; font-size: .875rem; font-weight: 500;
        }
        .alert-success { background: rgba(46,204,113,.1); border: 1px solid rgba(46,204,113,.25); color: var(--success); }
        .alert-error   { background: rgba(231,76,60,.1);  border: 1px solid rgba(231,76,60,.25);  color: var(--danger); }

        /* ── BUTTONS ── */
        .btn {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .6rem 1.2rem; border-radius: var(--radius);
            font-family: 'Barlow', sans-serif; font-size: .875rem; font-weight: 600;
            cursor: pointer; text-decoration: none; border: none; transition: all .15s;
        }
        .btn-primary { background: var(--accent); color: #111; }
        .btn-primary:hover { background: #ffc04a; transform: translateY(-1px); }
        .btn-ghost { background: transparent; color: var(--muted); border: 1px solid var(--border); }
        .btn-ghost:hover { color: var(--text); border-color: var(--muted); }
        .btn-danger { background: rgba(231,76,60,.12); color: var(--danger); border: 1px solid rgba(231,76,60,.25); }
        .btn-danger:hover { background: rgba(231,76,60,.22); }
        .btn-sm { padding: .4rem .8rem; font-size: .8rem; }

        /* ── FORM ELEMENTS ── */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem 1.5rem; }
        .field-group { display: flex; flex-direction: column; gap: .4rem; }
        .field-group label { font-size: .78rem; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); }
        .field-group .req { color: var(--accent); }
        .field-group .hint { font-size: .7rem; font-weight: 400; text-transform: none; letter-spacing: 0; opacity: .65; }
        .field-group input, .field-group select {
            background: var(--surface2); border: 1px solid var(--border);
            border-radius: var(--radius); color: var(--text);
            font-family: 'Barlow', sans-serif; font-size: .9rem;
            padding: .65rem .9rem; outline: none; transition: border-color .15s, box-shadow .15s; width: 100%;
        }
        .field-group input:focus, .field-group select:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(245,166,35,.1); }
        .field-group input::placeholder { color: var(--muted); opacity: .6; }
        .field-group.has-error input, .field-group.has-error select { border-color: var(--danger); }
        .field-error { font-size: .77rem; color: var(--danger); }
        .form-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 2rem; }
        .form-actions { display: flex; justify-content: flex-end; gap: .75rem; margin-top: 1.75rem; padding-top: 1.5rem; border-top: 1px solid var(--border); }
        .form-errors { background: rgba(231,76,60,.08); border: 1px solid rgba(231,76,60,.25); border-radius: var(--radius); padding: .9rem 1.2rem; margin-bottom: 1.5rem; }
        .form-errors ul { padding-left: 1.2rem; }
        .form-errors li { font-size: .875rem; color: var(--danger); margin-top: .2rem; }

        /* ── CARD ── */
        .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); }
        .card-header { padding: 1rem 1.25rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
        .card-title { font-family: 'Barlow Condensed', sans-serif; font-size: 1rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; }
        .card-body { padding: 1.25rem; }

        /* ── TABLE ── */
        .table-wrap { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; font-size: .875rem; }
        thead { background: var(--surface2); border-bottom: 2px solid var(--accent); }
        thead th { padding: .8rem 1rem; text-align: left; font-family: 'Barlow Condensed', sans-serif; font-size: .75rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--muted); white-space: nowrap; }
        tbody tr { border-bottom: 1px solid var(--border); transition: background .1s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: rgba(255,255,255,.025); }
        tbody td { padding: .85rem 1rem; vertical-align: middle; }
        .td-muted { color: var(--muted); font-size: .8rem; }
        .table-footer { padding: .75rem 1rem; background: var(--surface2); border-top: 1px solid var(--border); font-size: .78rem; color: var(--muted); display: flex; align-items: center; justify-content: space-between; }

        /* ── BADGES ── */
        .badge { display: inline-block; padding: .22rem .6rem; border-radius: 3px; font-size: .7rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
        .badge-admin  { background: rgba(232,56,13,.15); color: #f07c5a; border: 1px solid rgba(232,56,13,.3); }
        .badge-mec    { background: rgba(245,166,35,.12); color: var(--accent); border: 1px solid rgba(245,166,35,.25); }
        .badge-recep  { background: rgba(52,152,219,.12); color: #5dade2; border: 1px solid rgba(52,152,219,.25); }

        /* ── STAT CARDS ── */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.75rem; }
        .stat-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.25rem 1.5rem; }
        .stat-label { font-size: .72rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--muted); margin-bottom: .5rem; }
        .stat-value { font-family: 'Barlow Condensed', sans-serif; font-size: 2.2rem; font-weight: 800; color: var(--accent); line-height: 1; }
        .stat-sub { font-size: .78rem; color: var(--muted); margin-top: .35rem; }

        @media (max-width: 700px) {
            .sidebar { transform: translateX(-100%); }
            .main-wrap { margin-left: 0; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- ── SIDEBAR ── --}}
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-name">⚙ Taller <span>Automotriz</span></div>
        <div class="sidebar-brand-sub">Sistema de gestión</div>
    </div>

    <div class="sidebar-user">
        <div class="sidebar-user-name">{{ auth()->user()->nombre_usuario }}</div>
        <div class="sidebar-user-role">{{ auth()->user()->nombre_rol }}</div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">General</div>
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard*') ? 'active' : '' }}">
            <span class="nav-icon">⊞</span> Dashboard
        </a>

        <div class="nav-section">Consultas</div>
        <a href="{{ route('historial.index') }}" class="nav-item {{ request()->routeIs('historial*') ? 'active' : '' }}">
            <span class="nav-icon">📋</span> Historial
        </a>


        @if(auth()->user()->esAdmin())
        <div class="nav-section">Administración</div>
        <a href="{{ route('usuarios.index') }}" class="nav-item {{ request()->routeIs('usuarios*') ? 'active' : '' }}">
            <span class="nav-icon">👤</span> Usuarios
        </a>
        <a href="{{ route('bitacora.index') }}" class="nav-item {{ request()->routeIs('bitacora*') ? 'active' : '' }}">
            <span class="nav-icon">📋</span> Bitácora
        </a>
        @endif

        @if(auth()->user()->esAdmin() || auth()->user()->esRecepcionista())
        <div class="nav-section">Atención al cliente</div>
        <a href="{{ route('clientes.index') }}"
        class="nav-item {{ request()->routeIs('clientes*') ? 'active' : '' }}">
            <span class="nav-icon">🧑</span> Clientes
        </a>
        <a href="{{ route('autos.index') }}" class="nav-item {{ request()->routeIs('autos*') ? 'active' : '' }}">
            <span class="nav-icon">🚗</span> Vehículos
        </a>

        @endif


        {{-- Aquí puedes agregar más secciones según rol --}}
        @if(auth()->user()->esMecanico())
        <div class="nav-section">Taller</div>
        <a href="#" class="nav-item"><span class="nav-icon">🔧</span> Órdenes de Trabajo</a>
        @endif

        @if(auth()->user()->esRecepcionista())
        <div class="nav-section">Gestión</div>
        <a href="#" class="nav-item"><span class="nav-icon">📄</span> Proformas</a>
        @endif

    </nav>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-ghost" style="width:100%; justify-content:center;">
                ↩ Cerrar sesión
            </button>
        </form>
    </div>
</aside>

{{-- ── CONTENIDO PRINCIPAL ── --}}
<div class="main-wrap">
    <header class="topbar">
        <div class="topbar-title">@yield('title', 'Dashboard')</div>
        <div style="font-size:.8rem; color:var(--muted);">{{ now()->format('d/m/Y H:i') }}</div>
    </header>

    <main class="page-content">
        @if(session('success'))
            <div class="alert alert-success">✓ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">⚠ {{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</div>

@stack('scripts')
</body>
</html>
