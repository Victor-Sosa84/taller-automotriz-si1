<?php

use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\AutoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ── Raíz ──────────────────────────────────────────────────────
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// ── API interna (autenticada) ─────────────────────────────────
Route::middleware(['auth'])->group(function () {

    Route::get('/api/persona/{ci}', function (string $ci) {
        $persona = \App\Models\Persona::where('ci', $ci)->first();
        if (!$persona) return response()->json(null);
        return response()->json([
            'ci'          => $persona->ci,
            'nombre'      => $persona->nombre,
            'telefono'    => $persona->telefono,
            'direccion'   => $persona->direccion,
            'es_cliente'  => $persona->es_cliente,
            'es_personal' => $persona->es_personal,
        ]);
    })->name('api.persona');

    // ── Dashboard ─────────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Gestión de usuarios y permisos (solo id=1 o con permiso) ─
    Route::middleware(['permiso:CU13_BUS'])->group(function () {
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    });
    Route::middleware(['permiso:CU13_ADD'])->group(function () {
        Route::get('/usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios',       [UsuarioController::class, 'store'])->name('usuarios.store');
    });
    Route::middleware(['permiso:CU13_MOD'])->group(function () {
        Route::get('/usuarios/{id}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{id}',      [UsuarioController::class, 'update'])->name('usuarios.update');
    });
    Route::middleware(['permiso:CU13_ELI'])->group(function () {
        Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
    });

    // Cargos — parte de gestión de usuarios
    Route::get('/usuarios/{id}/cargos',           [CargoController::class, 'index'])->name('cargos.index');
    Route::post('/usuarios/{id}/cargos',          [CargoController::class, 'store'])->name('cargos.store');
    Route::delete('/usuarios/{id}/cargos/{tipo}', [CargoController::class, 'destroy'])->name('cargos.destroy');

    // Permisos (privilegios) — solo quien tenga CU13_PRI o sea id=1
    Route::middleware(['permiso:CU13_PRI'])->group(function () {
        Route::get('/permisos',                 [PermisoController::class, 'index'])->name('permisos.index');
        Route::post('/permisos/toggle',         [PermisoController::class, 'toggle'])->name('permisos.toggle');
        Route::post('/permisos/toggle-cu',      [PermisoController::class, 'toggleCU'])->name('permisos.toggleCU');
        Route::post('/permisos/toggle-paquete', [PermisoController::class, 'togglePaquete'])->name('permisos.togglePaquete');
    });

    // Roles (perfiles) — CRUD dinámico
    Route::middleware(['permiso:CU13_PRI'])->group(function () {
        Route::resource('roles', RolController::class)->except(['show']);
    });

    // Bitácora
    Route::middleware(['permiso:CU21_BUS'])->group(function () {
        Route::get('/bitacora', [BitacoraController::class, 'index'])->name('bitacora.index');
    });

    // ── Clientes ──────────────────────────────────────────────
    Route::middleware(['permiso:CU01_BUS'])->group(function () {
        Route::get('/clientes',       [ClienteController::class, 'index'])->name('clientes.index');
        Route::get('/clientes/{ci}',  [ClienteController::class, 'show'])->name('clientes.show');
    });
    Route::middleware(['permiso:CU01_ADD'])->group(function () {
        Route::get('/clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
        Route::post('/clientes',       [ClienteController::class, 'store'])->name('clientes.store');
    });
    Route::middleware(['permiso:CU01_MOD'])->group(function () {
        Route::get('/clientes/{ci}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
        Route::put('/clientes/{ci}',      [ClienteController::class, 'update'])->name('clientes.update');
    });

    // ── Vehículos ─────────────────────────────────────────────
    Route::middleware(['permiso:CU02_BUS'])->group(function () {
        Route::get('/autos',          [AutoController::class, 'index'])->name('autos.index');
        Route::get('/autos/{placa}',  [AutoController::class, 'show'])->name('autos.show');
    });
    Route::middleware(['permiso:CU02_ADD'])->group(function () {
        Route::get('/autos/create', [AutoController::class, 'create'])->name('autos.create');
        Route::post('/autos',       [AutoController::class, 'store'])->name('autos.store');
    });
    Route::middleware(['permiso:CU02_MOD'])->group(function () {
        Route::get('/autos/{placa}/edit', [AutoController::class, 'edit'])->name('autos.edit');
        Route::put('/autos/{placa}',      [AutoController::class, 'update'])->name('autos.update');
    });
    Route::middleware(['permiso:CU02_ELI'])->group(function () {
        Route::delete('/autos/{placa}', [AutoController::class, 'destroy'])->name('autos.destroy');
    });

    // ── Historial ─────────────────────────────────────────────
    Route::middleware(['permiso:CU03_BUS'])->group(function () {
        Route::get('/historial',          [HistorialController::class, 'index'])->name('historial.index');
        Route::get('/historial/{placa}',  [HistorialController::class, 'show'])->name('historial.show');
    });

});

require __DIR__.'/auth.php';