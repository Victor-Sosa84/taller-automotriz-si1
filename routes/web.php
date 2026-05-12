<?php

use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\AutoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiagnosticoController;
use App\Http\Controllers\DetalleDiagnosticoController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\OrdenTrabajoController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {

    // ── API interna ───────────────────────────────────────────
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

    Route::get('/api/rol/{id}/permisos', function (int $id) {
        $rol = \App\Models\Rol::with(['permisos' => function($q) {
            $q->wherePivot('estado', 'Activo');
        }])->findOrFail($id);
        return response()->json($rol->permisos->pluck('id'));
    })->name('api.rol.permisos');

    // ── Dashboard ─────────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Usuarios ─────────────────────────────────────────────
    Route::get('/usuarios',             [UsuarioController::class, 'index'])->name('usuarios.index')->middleware('permiso:CU13_BUS');
    Route::get('/usuarios/create',      [UsuarioController::class, 'create'])->name('usuarios.create')->middleware('permiso:CU13_ADD');
    Route::post('/usuarios',            [UsuarioController::class, 'store'])->name('usuarios.store')->middleware('permiso:CU13_ADD');
    Route::get('/usuarios/{id}/edit',   [UsuarioController::class, 'edit'])->name('usuarios.edit')->middleware('permiso:CU13_MOD');
    Route::put('/usuarios/{id}',        [UsuarioController::class, 'update'])->name('usuarios.update')->middleware('permiso:CU13_MOD');
    Route::delete('/usuarios/{id}',     [UsuarioController::class, 'destroy'])->name('usuarios.destroy')->middleware('permiso:CU13_ELI');

    // Cargos
    Route::get('/usuarios/{id}/cargos',           [CargoController::class, 'index'])->name('cargos.index');
    Route::post('/usuarios/{id}/cargos',          [CargoController::class, 'store'])->name('cargos.store');
    Route::delete('/usuarios/{id}/cargos/{tipo}', [CargoController::class, 'destroy'])->name('cargos.destroy');

    // ── Roles ────────────────────────────────────────────────
    Route::get('/roles',             [RolController::class, 'index'])->name('roles.index')->middleware('permiso:CU13_PRI');
    Route::get('/roles/create',      [RolController::class, 'create'])->name('roles.create')->middleware('permiso:CU13_PRI');
    Route::post('/roles',            [RolController::class, 'store'])->name('roles.store')->middleware('permiso:CU13_PRI');
    Route::get('/roles/{role}/edit', [RolController::class, 'edit'])->name('roles.edit')->middleware('permiso:CU13_PRI');
    Route::put('/roles/{role}',      [RolController::class, 'update'])->name('roles.update')->middleware('permiso:CU13_PRI');
    Route::delete('/roles/{role}',   [RolController::class, 'destroy'])->name('roles.destroy')->middleware('permiso:CU13_PRI');

    // ── Privilegios ───────────────────────────────────────────
    Route::get('/permisos',                 [PermisoController::class, 'index'])->name('permisos.index')->middleware('permiso:CU13_PRI');
    Route::post('/permisos/toggle',         [PermisoController::class, 'toggle'])->name('permisos.toggle')->middleware('permiso:CU13_PRI');
    Route::post('/permisos/toggle-cu',      [PermisoController::class, 'toggleCU'])->name('permisos.toggleCU')->middleware('permiso:CU13_PRI');
    Route::post('/permisos/toggle-paquete', [PermisoController::class, 'togglePaquete'])->name('permisos.togglePaquete')->middleware('permiso:CU13_PRI');

    // ── Bitácora ──────────────────────────────────────────────
    Route::get('/bitacora', [BitacoraController::class, 'index'])->name('bitacora.index')->middleware('permiso:CU21_BUS');

    // ── Clientes — rutas fijas ANTES que las dinámicas ────────
    Route::get('/clientes',            [ClienteController::class, 'index'])->name('clientes.index')->middleware('permiso:CU01_BUS');
    Route::get('/clientes/create',     [ClienteController::class, 'create'])->name('clientes.create')->middleware('permiso:CU01_ADD');
    Route::post('/clientes',           [ClienteController::class, 'store'])->name('clientes.store')->middleware('permiso:CU01_ADD');
    Route::get('/clientes/{ci}',       [ClienteController::class, 'show'])->name('clientes.show')->middleware('permiso:CU01_BUS');
    Route::get('/clientes/{ci}/edit',  [ClienteController::class, 'edit'])->name('clientes.edit')->middleware('permiso:CU01_MOD');
    Route::put('/clientes/{ci}',       [ClienteController::class, 'update'])->name('clientes.update')->middleware('permiso:CU01_MOD');

    // ── Vehículos — rutas fijas ANTES que las dinámicas ───────
    Route::get('/autos',               [AutoController::class, 'index'])->name('autos.index')->middleware('permiso:CU02_BUS');
    Route::get('/autos/create',        [AutoController::class, 'create'])->name('autos.create')->middleware('permiso:CU02_ADD');
    Route::post('/autos',              [AutoController::class, 'store'])->name('autos.store')->middleware('permiso:CU02_ADD');
    Route::get('/autos/{placa}',       [AutoController::class, 'show'])->name('autos.show')->middleware('permiso:CU02_BUS');
    Route::get('/autos/{placa}/edit',  [AutoController::class, 'edit'])->name('autos.edit')->middleware('permiso:CU02_MOD');
    Route::put('/autos/{placa}',       [AutoController::class, 'update'])->name('autos.update')->middleware('permiso:CU02_MOD');
    Route::delete('/autos/{placa}',    [AutoController::class, 'destroy'])->name('autos.destroy')->middleware('permiso:CU02_ELI');

    // ── Recepción Técnica ──────────────────────────────────────
    Route::get('/orden-trabajo/create', [OrdenTrabajoController::class, 'create'])->name('orden-trabajo.create')->middleware('permiso:CU04_ADD');
    Route::post('/orden-trabajo/store', [OrdenTrabajoController::class, 'store'])->name('orden-trabajo.store')->middleware('permiso:CU04_ADD');

    Route::get('/diagnostico/create',   [DiagnosticoController::class, 'create'])->name('diagnostico.create')->middleware('permiso:CU05_ADD');
    Route::post('/diagnostico/store',   [DiagnosticoController::class, 'store'])->name('diagnostico.store')->middleware('permiso:CU05_ADD');
    Route::post('/detalle-diagnostico/store', [DetalleDiagnosticoController::class, 'store'])->name('detalle-diagnostico.store')->middleware('permiso:CU05_ADD');

    // ── Historial ─────────────────────────────────────────────
    Route::get('/historial',           [HistorialController::class, 'index'])->name('historial.index')->middleware('permiso:CU03_BUS');
    Route::get('/historial/{placa}',   [HistorialController::class, 'show'])->name('historial.show')->middleware('permiso:CU03_BUS');

});

require __DIR__.'/auth.php';