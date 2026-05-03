<?php

use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\AutoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ── Raíz ──────────────────────────────────────────────────────
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// ── Rutas protegidas ──────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Dashboard — redirige según rol (no necesita permiso específico)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Solo Admin (rol:1) ────────────────────────────────────
    Route::middleware(['rol:1'])->group(function () {
        Route::get('/dashboard/admin', [DashboardController::class, 'admin'])->name('dashboard.admin');

        // Usuarios — protegidos por permiso
        Route::get('/usuarios',                  [UsuarioController::class, 'index'])->name('usuarios.index')->middleware('permiso:USU_VIEW');
        Route::get('/usuarios/create',           [UsuarioController::class, 'create'])->name('usuarios.create')->middleware('permiso:USU_CREATE');
        Route::post('/usuarios',                 [UsuarioController::class, 'store'])->name('usuarios.store')->middleware('permiso:USU_CREATE');
        Route::get('/usuarios/{id}/edit',        [UsuarioController::class, 'edit'])->name('usuarios.edit')->middleware('permiso:USU_EDIT');
        Route::put('/usuarios/{id}',             [UsuarioController::class, 'update'])->name('usuarios.update')->middleware('permiso:USU_EDIT');
        Route::delete('/usuarios/{id}',          [UsuarioController::class, 'destroy'])->name('usuarios.destroy')->middleware('permiso:USU_EDIT');

        // Cargos
        Route::get('/usuarios/{id}/cargos',          [CargoController::class, 'index'])->name('cargos.index');
        Route::post('/usuarios/{id}/cargos',         [CargoController::class, 'store'])->name('cargos.store');
        Route::delete('/usuarios/{id}/cargos/{tipo}', [CargoController::class, 'destroy'])->name('cargos.destroy');

        // Bitácora
        Route::get('/bitacora', [BitacoraController::class, 'index'])->name('bitacora.index')->middleware('permiso:BIT_VIEW');

        // Permisos
        Route::get('/permisos',         [PermisoController::class, 'index'])->name('permisos.index');
        Route::post('/permisos/toggle', [PermisoController::class, 'toggle'])->name('permisos.toggle');
    });

    // ── Solo Mecánico (rol:2) ─────────────────────────────────
    Route::middleware(['rol:2'])->group(function () {
        Route::get('/dashboard/mecanico', [DashboardController::class, 'mecanico'])->name('dashboard.mecanico');
    });

    // ── Solo Recepcionista (rol:3) ────────────────────────────
    Route::middleware(['rol:3'])->group(function () {
        Route::get('/dashboard/recepcionista', [DashboardController::class, 'recepcionista'])->name('dashboard.recepcionista');
    });

    // ── Admin y Recepcionista — Clientes ──────────────────────
    Route::middleware(['rol:1,3'])->group(function () {
        Route::get('/clientes',              [ClienteController::class, 'index'])->name('clientes.index')->middleware('permiso:CLI_VIEW');
        Route::get('/clientes/create',       [ClienteController::class, 'create'])->name('clientes.create')->middleware('permiso:CLI_CREATE');
        Route::post('/clientes',             [ClienteController::class, 'store'])->name('clientes.store')->middleware('permiso:CLI_CREATE');
        Route::get('/clientes/{ci}',         [ClienteController::class, 'show'])->name('clientes.show')->middleware('permiso:CLI_VIEW');
        Route::get('/clientes/{ci}/edit',    [ClienteController::class, 'edit'])->name('clientes.edit')->middleware('permiso:CLI_EDIT');
        Route::put('/clientes/{ci}',         [ClienteController::class, 'update'])->name('clientes.update')->middleware('permiso:CLI_EDIT');

        // Vehículos
        Route::get('/autos',              [AutoController::class, 'index'])->name('autos.index')->middleware('permiso:VEH_VIEW');
        Route::get('/autos/create',       [AutoController::class, 'create'])->name('autos.create')->middleware('permiso:VEH_CREATE');
        Route::post('/autos',             [AutoController::class, 'store'])->name('autos.store')->middleware('permiso:VEH_CREATE');
        Route::get('/autos/{placa}',      [AutoController::class, 'show'])->name('autos.show')->middleware('permiso:VEH_VIEW');
        Route::get('/autos/{placa}/edit', [AutoController::class, 'edit'])->name('autos.edit')->middleware('permiso:VEH_EDIT');
        Route::put('/autos/{placa}',      [AutoController::class, 'update'])->name('autos.update')->middleware('permiso:VEH_EDIT');
        Route::delete('/autos/{placa}',   [AutoController::class, 'destroy'])->name('autos.destroy')->middleware('permiso:VEH_DELETE');
    });

    // ── Todos los roles — Historial ───────────────────────────
    Route::get('/historial',         [HistorialController::class, 'index'])->name('historial.index')->middleware('permiso:HIST_VIEW');
    Route::get('/historial/{placa}', [HistorialController::class, 'show'])->name('historial.show')->middleware('permiso:HIST_VIEW');

});

require __DIR__.'/auth.php';