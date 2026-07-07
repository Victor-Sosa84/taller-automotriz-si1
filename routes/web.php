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
use App\Http\Controllers\ProformaController;
use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\DetalleOTController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\HerramientaController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\CuotaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\PagoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalidaVehiculoController;

// ── RUTA RAÍZ INDEPENDIENTE ───────────────────────────────────
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard.index') : redirect()->route('login');
});
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

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

// ── Dashboard Único ───────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/filtrar', [DashboardController::class, 'filtrarMetricas'])->name('dashboard.filtrar');
    Route::get('/dashboard/reporte', [DashboardController::class, 'exportarReporte'])->name('dashboard.reporte');
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
    Route::get('/diagnostico/{diagnostico}', [DiagnosticoController::class, 'show'])->name('diagnostico.show')->middleware('permiso:CU05_BUS');

    // ── Historial ─────────────────────────────────────────────
    Route::get('/historial',           [HistorialController::class, 'index'])->name('historial.index')->middleware('permiso:CU03_BUS');
    Route::get('/historial/{placa}',   [HistorialController::class, 'show'])->name('historial.show')->middleware('permiso:CU03_BUS');

    // CU-06, CU-07, CU-08
    Route::get('/proforma/crear', [ProformaController::class, 'create'])->name('proforma.create')->middleware('permiso:CU06_ADD');
    Route::post('/proforma', [ProformaController::class, 'store'])->name('proforma.store')->middleware('permiso:CU06_ADD');
    Route::get('/proforma/{proforma}', [ProformaController::class, 'show'])->name('proforma.show')->middleware('permiso:CU06_BUS');
    Route::get('/proforma/{proforma}/editar', [ProformaController::class, 'edit'])->name('proforma.edit')->middleware('permiso:CU06_MOD');
    Route::put('/proforma/{proforma}', [ProformaController::class, 'update'])->name('proforma.update')->middleware('permiso:CU06_MOD');
    Route::delete('/proforma/{proforma}', [ProformaController::class, 'destroy'])->name('proforma.destroy')->middleware('permiso:CU06_DEL');
    Route::post('/proforma/{proforma}/emitir', [ProformaController::class, 'emitir'])->name('proforma.emitir')->middleware('permiso:CU07_ADD');
    Route::post('/proforma/{proforma}/estado', [ProformaController::class, 'actualizarEstado'])->name('proforma.estado')->middleware('permiso:CU08_MOD');
    Route::get('/proformas', [ProformaController::class, 'index'])->name('proforma.index')->middleware('permiso:CU06_BUS');
    Route::get('/proforma/{proforma}/pdf', [ProformaController::class, 'pdf'])->name('proforma.pdf')->middleware('permiso:CU07_GEN');
    
    // ── CU-14 Gestionar orden de trabajo ─────────────────────────────────────
    Route::get('/ordenes',              [OrdenTrabajoController::class, 'obtenerOrdenes'])->name('orden_trabajo.index')->middleware('permiso:CU14_BUS');
    Route::get('/ordenes/{nro}',        [OrdenTrabajoController::class, 'obtenerOrden'])->name('orden_trabajo.show')->middleware('permiso:CU14_BUS');
    Route::put('/ordenes/{nro}',        [OrdenTrabajoController::class, 'actualizarOrden'])->name('orden_trabajo.update')->middleware('permiso:CU14_MOD');
    Route::get('/ordenes/{nro}/editar', [OrdenTrabajoController::class, 'editarOrden'])->name('orden_trabajo.edit')->middleware('permiso:CU14_MOD');
    
    // ── CU-15 Asignación responsables a tareas ─────────────────────────────────────
    Route::get('/ordenes/{nro}/asignaciones', [AsignacionController::class, 'obtenerAsignaciones'])->name('asignacion.index')->middleware('permiso:CU15_BUS');
    Route::post('/ordenes/{nro}/asignaciones', [AsignacionController::class, 'registrarAsignacion'])->name('asignacion.store')->middleware('permiso:CU15_ADD');
    Route::put('/ordenes/{nro}/asignaciones/{ci}/{idManoObra}', [AsignacionController::class, 'actualizarAsignacion'])->name('asignacion.update')->middleware('permiso:CU15_MOD');
    Route::delete('/ordenes/{nro}/asignaciones/{ci}/{idManoObra}', [AsignacionController::class, 'eliminarAsignacion'])->name('asignacion.destroy')->middleware('permiso:CU15_DEL');
    
    // ── CU-16 Registrar repuestos y mano de obra ─────────────────────────────────────
    Route::get('/ordenes/{nro}/detalles', [DetalleOTController::class, 'obtenerDetalles'])->name('detalle_ot.index')->middleware('permiso:CU16_BUS');
    Route::post('/ordenes/{nro}/detalles', [DetalleOTController::class, 'registrarDetalles'])->name('detalle_ot.store')->middleware('permiso:CU16_ADD');
    Route::put('/ordenes/{nro}/detalles/{tipo}/{id}', [DetalleOTController::class, 'editarDetalles'])->name('detalle_ot.update')->middleware('permiso:CU16_MOD');
    Route::delete('/ordenes/{nro}/detalles/{tipo}/{id}', [DetalleOTController::class, 'eliminarDetalles'])->name('detalle_ot.destroy')->middleware('permiso:CU16_DEL');
    
    // ── Gestionar catálogo de taller ─────────────────────────────────────
    Route::get('/catalogos/taller', [CatalogoController::class, 'taller'])->name('catalogo.taller')->middleware('permiso:CU13_PRI');
    Route::post('/catalogos/repuestos', [CatalogoController::class, 'storeRepuesto'])->name('catalogo.repuesto.store')->middleware('permiso:CU13_PRI');
    Route::put('/catalogos/repuestos/{id}', [CatalogoController::class, 'updateRepuesto'])->name('catalogo.repuesto.update')->middleware('permiso:CU13_PRI');
    Route::delete('/catalogos/repuestos/{id}', [CatalogoController::class, 'destroyRepuesto'])->name('catalogo.repuesto.destroy')->middleware('permiso:CU13_PRI');
    Route::post('/catalogos/mano-obra', [CatalogoController::class, 'storeManoObra'])->name('catalogo.mo.store')->middleware('permiso:CU13_PRI');
    Route::put('/catalogos/mano-obra/{id}', [CatalogoController::class, 'updateManoObra'])->name('catalogo.mo.update')->middleware('permiso:CU13_PRI');
    Route::delete('/catalogos/mano-obra/{id}', [CatalogoController::class, 'destroyManoObra'])->name('catalogo.mo.destroy')->middleware('permiso:CU13_PRI');
    Route::post('/catalogos/herramientas', [CatalogoController::class, 'storeHerramienta'])->name('catalogo.herramienta.store')->middleware('permiso:CU13_PRI');
    Route::put('/catalogos/herramientas/{nro}', [CatalogoController::class, 'updateHerramienta'])->name('catalogo.herramienta.update')->middleware('permiso:CU13_PRI');
    Route::delete('/catalogos/herramientas/{nro}', [CatalogoController::class, 'destroyHerramienta'])->name('catalogo.herramienta.destroy')->middleware('permiso:CU13_PRI');
    Route::post('/catalogos/tipos', [CatalogoController::class, 'storeTipo'])->name('catalogo.tipo.store')->middleware('permiso:CU13_PRI');
    Route::put('/catalogos/tipos/{id}', [CatalogoController::class, 'updateTipo'])->name('catalogo.tipo.update')->middleware('permiso:CU13_PRI');
    Route::delete('/catalogos/tipos/{id}', [CatalogoController::class, 'destroyTipo'])->name('catalogo.tipo.destroy')->middleware('permiso:CU13_PRI');
    Route::post('/catalogos/marcas', [CatalogoController::class, 'storeMarca'])->name('catalogo.marca.store')->middleware('permiso:CU13_PRI');
    Route::put('/catalogos/marcas/{id}', [CatalogoController::class, 'updateMarca'])->name('catalogo.marca.update')->middleware('permiso:CU13_PRI');
    Route::delete('/catalogos/marcas/{id}', [CatalogoController::class, 'destroyMarca'])->name('catalogo.marca.destroy')->middleware('permiso:CU13_PRI');
    
    // ── CU-09 Gestionar préstamo de herramientas ─────────────────────────────────────
    Route::get('/prestamos', [PrestamoController::class, 'obtenerPrestamos'])->name('prestamo.index')->middleware('permiso:CU09_BUS');
    Route::post('/prestamos', [PrestamoController::class, 'registrarPrestamos'])->name('prestamo.store')->middleware('permiso:CU09_ADD');
    Route::delete('/prestamos/{id}', [PrestamoController::class, 'eliminarPrestamo'])->name('prestamo.destroy')->middleware('permiso:CU09_DEL');

    // ── CU-10 Gestionar estado de herramientas ─────────────────────────────────────
    Route::put('/prestamos/{id}/devolucion', [HerramientaController::class, 'actualizarEstado'])->name('herramienta.devolucion')->middleware('permiso:CU10_MOD');
    
    // ── CU-17 Generar factura final ─────────────────────────────────────
    Route::get('/ordenes/{nro}/factura/crear', [FacturaController::class, 'verDetalleFactura'])->name('factura.create')->middleware('permiso:CU17_GEN');
    Route::post('/ordenes/{nro}/factura', [FacturaController::class, 'crearFactura'])->name('factura.store')->middleware('permiso:CU17_GEN');
    Route::get('/facturas/{nro}', [FacturaController::class, 'mostrarFactura'])->name('factura.show')->middleware('permiso:CU17_BUS');
    Route::get('/facturas/{factura}/pdf', [FacturaController::class, 'pdf'])->name('factura.pdf')->middleware('permiso:CU17_BUS');
    
    // ── CU-18 Registrar pago y cuotas ─────────────────────────────────────
    Route::get('/facturas/{nro}/pago', [CuotaController::class, 'mostrarPago'])->name('cuota.create')->middleware('permiso:CU18_ADD');
    Route::post('/facturas/{nro}/pago', [CuotaController::class, 'registrarPago'])->name('cuota.store')->middleware('permiso:CU18_ADD');
    Route::post('/api/cuota/intento-pago', [CuotaController::class, 'crearIntentoPago'])->name('api.cuota.intento_pago');

    // ── CU-22 Generar reportes por comando de voz ──────────────────────────
    Route::post('/api/reporte/consultar', [ReporteController::class, 'consultarReporte'])->name('api.reporte.consultar')->middleware('permiso:CU22_GEN');
    Route::post('/api/reporte/exportar', [ReporteController::class, 'exportarReporte'])->name('api.reporte.exportar')->middleware('permiso:CU22_GEN');

    // ── CU-11 Gestionar Contratos de Trabajo ───────────────────────────────
    Route::controller(ContratoController::class)->group(function () {
        Route::get('/contratos', 'listarContratos')->name('contratos.index')->middleware('permiso:CU11_BUS');
        Route::post('/contratos/guardar', 'crearContrato')->name('contratos.store')->middleware('permiso:CU11_ADD');
        Route::get('/contratos/{id}/ver', 'verContrato')->name('contratos.show')->middleware('permiso:CU11_BUS');
        Route::post('/contratos/{id}/actualizar', 'actualizarContrato')->name('contratos.update')->middleware('permiso:CU11_MOD');
        Route::post('/contratos/{id}/baja', 'darBajaContrato')->name('contratos.baja')->middleware('permiso:CU11_ELI');
    });

    // ── CU-12 Liquidar Pagos de Personal ───────────────────────────────────
    Route::controller(PagoController::class)->group(function () {
        Route::get('/pagos', 'listarPagos')->name('pagos.index')->middleware('permiso:CU12_BUS');
        Route::get('/pagos/{id_contrato}/calcular', 'calcularPago')->name('pagos.calculate')->middleware('permiso:CU12_BUS');
        Route::post('/pagos/guardar', 'mostrarPago')->name('pagos.store')->middleware('permiso:CU12_ADD'); // Este procesa el envío de la liquidación
    });


    // 🚗 Gestión de Salida de Vehículos
    Route::get('/salida-vehiculos', [SalidaVehiculoController::class, 'listarTrabajos'])->name('salida.index');
    Route::post('/salida-vehiculos/verificar', [SalidaVehiculoController::class, 'mostrarTrabajo'])->name('salida.verificar');
    Route::post('/salida-vehiculos/registrar', [SalidaVehiculoController::class, 'registrarSalida'])->name('salida.registrar');
    Route::get('/salida-vehiculos/imprimir/{nro_orden}', [SalidaVehiculoController::class, 'imprimirSalida'])->name('salida.imprimir');



    });

require __DIR__.'/auth.php';
