<?php

use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\AutoController;
use App\Http\Controllers\HistorialController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ── Ruta raíz → login si no autenticado ──────────────────────
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});


// ── Login ─────────────────────────────────────────────────────
Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = [
        'correo' => $request->correo,
        'password' => $request->clave,
    ];

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->route('dashboard');
    }

    return back()->withErrors([
        'correo' => 'Credenciales incorrectas.',
    ])->withInput();
})->name('login.submit');

// ── Logout ────────────────────────────────────────────────────
Route::post('/logout', function (\Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// ── Rutas públicas ───────────────────────────────────────────
Route::get('/historial',        [HistorialController::class, 'index'])->name('historial.index');
Route::get('/historial/{placa}', [HistorialController::class, 'show'])->name('historial.show');

// ── Rutas protegidas ──────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // El "index" no necesita rol específico porque él es quien redirige
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 🔒 SOLO ADMIN
    Route::middleware(['rol:1'])->group(function () {
        Route::get('/dashboard/admin', [DashboardController::class, 'admin'])->name('dashboard.admin');
        Route::resource('usuarios', UsuarioController::class)->except(['show']);
        Route::get('/bitacora', [BitacoraController::class, 'index'])->name('bitacora.index');
    });

    // 🔒 SOLO MECÁNICO
    Route::middleware(['rol:2'])->group(function () {
        Route::get('/dashboard/mecanico', [DashboardController::class, 'mecanico'])->name('dashboard.mecanico');
    });

    // 🔒 SOLO RECEPCIONISTA
    Route::middleware(['rol:3'])->group(function () {
        Route::get('/dashboard/recepcionista', [DashboardController::class, 'recepcionista'])->name('dashboard.recepcionista');
    });

        // 🔒 Admin y Recepcionista (roles 1 y 3)
    Route::middleware(['rol:1,3'])->group(function () {
        Route::resource('clientes', ClienteController::class)->except(['destroy']);
        Route::resource('autos', AutoController::class); // CRUD completo para autos
    });

});

// ── Rutas de autenticación (login, register, etc.) ─────────────────
require __DIR__.'/auth.php';