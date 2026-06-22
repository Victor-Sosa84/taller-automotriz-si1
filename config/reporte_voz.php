<?php

use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\AutoController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CuotaController;
use App\Http\Controllers\DiagnosticoController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\OrdenTrabajoController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\ProformaController;

/*
|--------------------------------------------------------------------------
| Catálogo de funciones — CU-22: Generar reportes por comando de voz
|--------------------------------------------------------------------------
|
| Mapa cerrado de las funciones que el ServicioInterpretacionIA puede
| elegir invocar. Cada entrada indica el controller y método real que
| la implementa, y el permiso que el usuario debe tener para ejecutarla.
|
| IMPORTANTE: este archivo es la whitelist de seguridad de CU-22.
| El permiso CU22_GEN solo habilita el acceso al comando de voz en sí;
| el permiso indicado aquí es el que realmente autoriza el dato consultado.
| Si un nombre de función no aparece en este mapa, ReporteController debe
| rechazar la ejecución, incluso si el JSON de la IA lo menciona.
|
*/

return [

    'buscarProformasPorEstado' => [
        'controller' => ProformaController::class,
        'metodo'     => 'buscarProformasPorEstado',
        'permiso'    => 'CU06_BUS',
    ],

    'buscarProformaPorNumero' => [
        'controller' => ProformaController::class,
        'metodo'     => 'buscarProformaPorNumero',
        'permiso'    => 'CU06_BUS',
    ],

    'buscarFacturasPorPeriodo' => [
        'controller' => FacturaController::class,
        'metodo'     => 'buscarFacturasPorPeriodo',
        'permiso'    => 'CU17_BUS',
    ],

    'buscarFacturaPorNumero' => [
        'controller' => FacturaController::class,
        'metodo'     => 'buscarFacturaPorNumero',
        'permiso'    => 'CU17_BUS',
    ],

    'buscarCuotasPendientes' => [
        'controller' => CuotaController::class,
        'metodo'     => 'buscarCuotasPendientes',
        'permiso'    => 'CU18_BUS',
    ],

    'buscarOrdenesPorEstado' => [
        'controller' => OrdenTrabajoController::class,
        'metodo'     => 'buscarOrdenesPorEstado',
        'permiso'    => 'CU14_BUS',
    ],

    'buscarPrestamos' => [
        'controller' => PrestamoController::class,
        'metodo'     => 'buscarPrestamos',
        'permiso'    => 'CU09_BUS',
    ],

    'buscarAsignacionesPorOrden' => [
        'controller' => AsignacionController::class,
        'metodo'     => 'buscarAsignacionesPorOrden',
        'permiso'    => 'CU15_BUS',
    ],

    'buscarDiagnosticosPorPeriodo' => [
        'controller' => DiagnosticoController::class,
        'metodo'     => 'buscarDiagnosticosPorPeriodo',
        'permiso'    => 'CU05_BUS',
    ],

    // Catálogo general — sin permiso adicional, basta con CU22_GEN
    'buscarCatalogo' => [
        'controller' => CatalogoController::class,
        'metodo'     => 'buscarCatalogo',
        'permiso'    => null,
    ],

    'buscarAutosCatalogo' => [
        'controller' => AutoController::class,
        'metodo'     => 'buscarAutosCatalogo',
        'permiso'    => null,
    ],

    'buscarTiposTrabajadorCatalogo' => [
        'controller' => CargoController::class,
        'metodo'     => 'buscarTiposTrabajadorCatalogo',
        'permiso'    => null,
    ],

    // Agregación — sin permiso adicional, solo conteos sin datos individuales
    'contarClientesPorZona' => [
        'controller' => ClienteController::class,
        'metodo'     => 'contarClientesPorZona',
        'permiso'    => null,
    ],

    'contarPersonalPorTipoTrabajador' => [
        'controller' => CargoController::class,
        'metodo'     => 'contarPersonalPorTipoTrabajador',
        'permiso'    => null,
    ],

];
