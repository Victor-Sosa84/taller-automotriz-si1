<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// MODELOS DE TU SISTEMA
use App\Models\Bitacora;
use App\Models\Persona;
use App\Models\Usuario;
use App\Models\OrdenTrabajo;
use App\Models\Cuota;
use App\Models\DetalleRepuesto;
use App\Models\Realiza;

class DashboardController extends Controller
{
    /**
     * INDEX: Reservado para la carga inicial de la página y comandos de voz.
     */
    public function index(Request $request)
    {
        $usuario = Auth::user();

        $stats = [
            'totalUsuarios' => $usuario->puede('CU13_BUS') ? Usuario::count() : null,
            'totalClientes' => $usuario->puede('CU01_BUS') ? Persona::where('es_cliente', true)->count() : null,
            'totalPersonal' => $usuario->puede('CU13_BUS') ? Persona::where('es_personal', true)->count() : null,
        ];

        $ultimasBitacoras = $usuario->puede('CU21_BUS')
            ? Bitacora::with('usuario')->orderByDesc('fecha_hora')->limit(5)->get()
            : collect();

        // Enviamos las métricas calculadas por si la vista o el comando de voz las requieren de entrada
        $metricas = $this->calcularMetricas();

        return view('dashboard.index', compact('stats', 'ultimasBitacoras', 'metricas'));
    }

    /**
     * CU23 - Flujo: 2. aplicarFiltro() -> 2.1 filtrarMetricas()
     * ESTE MÉTODO ALIMENTA TU GRÁFICA WEB VÍA AJAX.
     * Retorna exactamente el formato de objetos que tu JavaScript sabe procesar.
     */
public function filtrarMetricas(Request $request)
{
    // 1. Capturamos el input de la fecha
    $fechaInput = $request->input('fecha');

    try {
        if (!empty($fechaInput)) {
            // Carbon detectará inteligentemente si viene como YYYY-MM-DD o DD/MM/YYYY y lo estandarizará
            $fecha = \Carbon\Carbon::parse($fechaInput)->format('Y-m-d');
        } else {
            $fecha = '2026-07-06';
        }
    } catch (\Exception $e) {
        // Si falla el parseo por algún formato extraño, aseguramos el día de hoy hardcodeado
        $fecha = '2026-07-06';
    }

    // 2. Órdenes del día usando la fecha limpia estandarizada
    $ordenesFiltradas = OrdenTrabajo::whereRaw("DATE(fecha_inicio) = ?", [$fecha])->get();
    $idsOrdenes = $ordenesFiltradas->pluck('nro')->toArray(); 

    // 3. Cuotas del día
    $cuotasFiltradas = Cuota::whereRaw("DATE(fecha) = ?", [$fecha])->get();
    
    // 4. Repuestos y Realiza vinculados
    $repuestosFiltrados = collect();
    $realizaFiltrado = collect();

    if (!empty($idsOrdenes)) {
        $repuestosFiltrados = DetalleRepuesto::whereIn('nro_orden_trabajo', $idsOrdenes)->get();
        $realizaFiltrado = Realiza::whereIn('nro_orden_trabajo', $idsOrdenes)->get(); 
    }

    return response()->json([
        'ordenes'   => $ordenesFiltradas,
        'cuotas'    => $cuotasFiltradas,
        'repuestos' => $repuestosFiltrados,
        'realiza'   => $realizaFiltrado,
        'fecha_procesada_backend' => $fecha // Esto te servirá para ver en Network qué fecha entendió Laravel
    ]);
}
    /**
     * CU23 - Flujo: 1.1 calcularMetricas()
     * Mantiene consistencia con los requerimientos de tu caso de uso histórico global.
     */
    public function calcularMetricas()
    {
        $totalOrdenes = OrdenTrabajo::count();
        $ingresosCuotas = (float) Cuota::sum('monto');
        $totalRepuestosUsados = (int) DetalleRepuesto::sum('cantidad');
        $mecanicosActivos = (int) Realiza::distinct('ci_personal')->count('ci_personal'); 

        return [
            'totalOrdenes'         => $totalOrdenes,
            'ingresosCuotas'       => $ingresosCuotas,
            'totalRepuestosUsados' => $totalRepuestosUsados,
            'mecanicosActivos'     => $mecanicosActivos,
        ];
    }

    /**
     * CU23 - Generar Reporte PDF del Dashboard
     */
    public function exportarReporte(Request $request)
    {
        $fecha = $request->input('fecha', '2026-07-06');

        $ordenesFiltradas = OrdenTrabajo::whereRaw("DATE(fecha_inicio) = ?", [$fecha])->get();
        $idsOrdenes = $ordenesFiltradas->pluck('nro')->toArray();

        $metricas = [
            'totalOrdenes'         => $ordenesFiltradas->count(),
            'ingresosCuotas'       => (float) Cuota::whereRaw("DATE(fecha) = ?", [$fecha])->sum('monto'),
            'totalRepuestosUsados' => !empty($idsOrdenes) ? DetalleRepuesto::whereIn('nro_orden_trabajo', $idsOrdenes)->sum('cantidad') : 0,
            'mecanicosActivos'     => !empty($idsOrdenes) ? Realiza::whereIn('nro_orden_trabajo', $idsOrdenes)->distinct('ci_personal')->count('ci_personal') : 0,
            'fecha_filtro'         => $fecha
        ];

        $pdf = Pdf::loadView('reportes.dashboard_pdf', compact('metricas'));
        return $pdf->download('dashboard.pdf');
    }
}