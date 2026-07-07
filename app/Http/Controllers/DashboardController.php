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
    try {
        $fechaInput = $request->input('fecha');

        // Creación de las queries bases
        $queryIngresos = DB::table('cuota');
        $queryEstados  = DB::table('orden_trabajo');
        $queryMecanicos = DB::table('realiza')
            ->join('persona', 'realiza.ci_personal', '=', 'persona.ci');
        $queryRepuestos = DB::table('detalle_repuesto')
            ->join('repuesto', 'detalle_repuesto.id_repuesto', '=', 'repuesto.id');

        // 🕒 CONTROL DE FECHAS INTELIGENTE: 
        // Si el usuario pasa una fecha, filtramos ese mes. Si viene vacío, extrae el Histórico Completo.
        if (!empty($fechaInput)) {
            $fecha = \Carbon\Carbon::parse($fechaInput);
            $mes = $fecha->month;
            $anio = $fecha->year;

            // Filtros aplicados por rango de mes seleccionado
            $queryIngresos->whereMonth('fecha', $mes)->whereYear('fecha', $anio);
            $queryEstados->whereMonth('fecha_inicio', $mes)->whereYear('fecha_inicio', $anio);
            
            // Unimos a orden_trabajo en las tablas relacionales para poder filtrar por su fecha_inicio
            $queryMecanicos->join('orden_trabajo', 'realiza.nro_orden_trabajo', '=', 'orden_trabajo.nro')
                           ->whereMonth('orden_trabajo.fecha_inicio', $mes)->whereYear('orden_trabajo.fecha_inicio', $anio);
            
            $queryRepuestos->join('orden_trabajo', 'detalle_repuesto.nro_orden_trabajo', '=', 'orden_trabajo.nro')
                           ->whereMonth('orden_trabajo.fecha_inicio', $mes)->whereYear('orden_trabajo.fecha_inicio', $anio);

            // Formato de agrupación por día
            $queryIngresos->select(DB::raw("DATE_FORMAT(fecha, '%d/%m') as periodo"), DB::raw('SUM(monto) as total'));
        } else {
            // Formato de agrupación global por Año-Mes
            $queryIngresos->select(DB::raw("DATE_FORMAT(fecha, '%Y-%m') as periodo"), DB::raw('SUM(monto) as total'));
            $queryMecanicos->join('orden_trabajo', 'realiza.nro_orden_trabajo', '=', 'orden_trabajo.nro');
            $queryRepuestos->join('orden_trabajo', 'detalle_repuesto.nro_orden_trabajo', '=', 'orden_trabajo.nro');
        }

        // Ejecución ordenada de la recopilación de datos
        $ingresos = $queryIngresos->groupBy('periodo')->orderBy('periodo', 'asc')->get();
        $estados  = $queryEstados->select('estado', DB::raw('count(*) as total'))->groupBy('estado')->get();
        
        $mecanicos = $queryMecanicos->select('persona.nombre', DB::raw('COUNT(realiza.nro_orden_trabajo) as trabajos_realizados'))
            ->groupBy('persona.ci', 'persona.nombre')->orderBy('trabajos_realizados', 'desc')->limit(5)->get();
            
        $repuestos = $queryRepuestos->select('repuesto.nombre', DB::raw('SUM(detalle_repuesto.cantidad) as total_usado'))
            ->groupBy('repuesto.id', 'repuesto.nombre')->orderBy('total_usado', 'desc')->limit(5)->get();

        return response()->json([
            'success' => true,
            'reporte_ingresos' => $ingresos,
            'reporte_estados'  => $estados,
            'reporte_mecanicos'=> $mecanicos,
            'reporte_repuestos'=> $repuestos
        ]);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
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