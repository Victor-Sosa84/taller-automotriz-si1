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
     * ESTE MÉTODO ALIMENTA la GRÁFICA WEB VÍA AJAX.
     * Retorna exactamente el formato de objetos que tu JavaScript sabe procesar.
     */
    public function filtrarMetricas(Request $request)
{
    try {
        $fechaInput = $request->input('fecha');
        $periodoInput = $request->input('periodo'); // dia | semana | mes | anio

        // Creación de las queries bases
        $queryIngresos = DB::table('cuota');
        $queryEstados  = DB::table('orden_trabajo');
        $queryMecanicos = DB::table('realiza')
            ->join('persona', 'realiza.ci_personal', '=', 'persona.ci');
        $queryRepuestos = DB::table('detalle_repuesto')
            ->join('repuesto', 'detalle_repuesto.id_repuesto', '=', 'repuesto.id');

        // 🕒 CONTROL DE FECHAS INTELIGENTE:
        // Si el usuario pasa una fecha, calculamos el rango [inicio, fin] según el período elegido.
        // Si viene vacío, extrae el Histórico Completo (sin límite de fechas).
        if (!empty($fechaInput)) {
            [$inicio, $fin, $formatoAgrupacion] = $this->resolverRangoPeriodo($fechaInput, $periodoInput);

            $queryIngresos->whereBetween('fecha', [$inicio, $fin]);
            $queryEstados->whereBetween('fecha_inicio', [$inicio, $fin]);

            // Unimos a orden_trabajo en las tablas relacionales para poder filtrar por su fecha_inicio
            $queryMecanicos->join('orden_trabajo', 'realiza.nro_orden_trabajo', '=', 'orden_trabajo.nro')
                           ->whereBetween('orden_trabajo.fecha_inicio', [$inicio, $fin]);

            $queryRepuestos->join('orden_trabajo', 'detalle_repuesto.nro_orden_trabajo', '=', 'orden_trabajo.nro')
                           ->whereBetween('orden_trabajo.fecha_inicio', [$inicio, $fin]);

            $queryIngresos->select(DB::raw("DATE_FORMAT(fecha, '{$formatoAgrupacion}') as periodo"), DB::raw('SUM(monto) as total'));
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

    } catch (\Throwable $e) {
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
        $fechaInput = $request->input('fecha', now()->format('Y-m-d'));
        $periodoInput = $request->input('periodo', 'mes');

        [$inicio, $fin, $formatoAgrupacion] = $this->resolverRangoPeriodo($fechaInput, $periodoInput);

        // Mismas 4 fuentes que alimentan las gráficas web (filtrarMetricas), para que el PDF calque lo filtrado en pantalla
        $ingresos = DB::table('cuota')
            ->whereBetween('fecha', [$inicio, $fin])
            ->select(DB::raw("DATE_FORMAT(fecha, '{$formatoAgrupacion}') as periodo"), DB::raw('SUM(monto) as total'))
            ->groupBy('periodo')->orderBy('periodo', 'asc')->get();

        $estados = DB::table('orden_trabajo')
            ->whereBetween('fecha_inicio', [$inicio, $fin])
            ->select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')->get();

        $mecanicos = DB::table('realiza')
            ->join('persona', 'realiza.ci_personal', '=', 'persona.ci')
            ->join('orden_trabajo', 'realiza.nro_orden_trabajo', '=', 'orden_trabajo.nro')
            ->whereBetween('orden_trabajo.fecha_inicio', [$inicio, $fin])
            ->select('persona.nombre', DB::raw('COUNT(realiza.nro_orden_trabajo) as trabajos_realizados'))
            ->groupBy('persona.ci', 'persona.nombre')->orderBy('trabajos_realizados', 'desc')->limit(5)->get();

        $repuestos = DB::table('detalle_repuesto')
            ->join('repuesto', 'detalle_repuesto.id_repuesto', '=', 'repuesto.id')
            ->join('orden_trabajo', 'detalle_repuesto.nro_orden_trabajo', '=', 'orden_trabajo.nro')
            ->whereBetween('orden_trabajo.fecha_inicio', [$inicio, $fin])
            ->select('repuesto.nombre', DB::raw('SUM(detalle_repuesto.cantidad) as total_usado'))
            ->groupBy('repuesto.id', 'repuesto.nombre')->orderBy('total_usado', 'desc')->limit(5)->get();

        $metricas = [
            'fecha_filtro'  => $fechaInput,
            'periodo'       => $periodoInput,
            'rango_inicio'  => $inicio->format('d/m/Y'),
            'rango_fin'     => $fin->format('d/m/Y'),
        ];

        // Generamos las 4 imágenes de las gráficas (mismos tipos y colores que la vista web) vía QuickChart
        $imgIngresos  = $this->generarImagenGrafica('line', $ingresos->pluck('periodo'), $ingresos->pluck('total'), 'Recaudado ($)', '#f29436', 'rgba(242,148,54,0.15)');
        $imgEstados   = $this->generarImagenGraficaDona($estados->pluck('estado'), $estados->pluck('total'));
        $imgMecanicos = $this->generarImagenGrafica('bar', $mecanicos->pluck('nombre'), $mecanicos->pluck('trabajos_realizados'), 'Órdenes Concluidas', '#36a2eb', '#36a2eb', true);
        $imgRepuestos = $this->generarImagenGrafica('bar', $repuestos->pluck('nombre'), $repuestos->pluck('total_usado'), 'Unidades Usadas', '#e74a3b', '#e74a3b');

        $pdf = Pdf::loadView('reportes.dashboard_pdf', compact(
            'metricas', 'ingresos', 'estados', 'mecanicos', 'repuestos',
            'imgIngresos', 'imgEstados', 'imgMecanicos', 'imgRepuestos'
        ));
        return $pdf->download('dashboard.pdf');
    }




    /**
     * Resuelve el rango [inicio, fin] y el formato de agrupación (DATE_FORMAT)
     * según el período elegido, tomando la fecha del <input type="date"> como referencia.
     * Semana: Domingo a Sábado.
     */
    private function resolverRangoPeriodo(string $fechaInput, ?string $periodo): array
    {
        $fecha = \Carbon\Carbon::parse($fechaInput);
        $periodo = $periodo ?: 'mes'; // valor por defecto si no llega el parámetro

        switch ($periodo) {
            case 'dia':
                $inicio = $fecha->copy()->startOfDay();
                $fin    = $fecha->copy()->endOfDay();
                $formato = '%H:00'; // agrupación horaria dentro del día
                break;

            case 'semana':
                $inicio = $fecha->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
                $fin    = $fecha->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
                $formato = '%d/%m'; // 7 puntos, uno por día
                break;

            case 'anio':
                $inicio = $fecha->copy()->startOfYear();
                $fin    = $fecha->copy()->endOfYear();
                $formato = '%Y-%m'; // 12 puntos, uno por mes
                break;

            case 'mes':
            default:
                $inicio = $fecha->copy()->startOfMonth();
                $fin    = $fecha->copy()->endOfMonth();
                $formato = '%d/%m'; // un punto por día del mes
                break;
        }

        return [$inicio, $fin, $formato];
    }


    /**
     * Genera una imagen PNG (línea o barras) vía QuickChart.io replicando la config de Chart.js de la vista web.
     * Retorna un data URI base64 listo para usar en <img src="...">, o null si falla la llamada externa.
     */
    private function generarImagenGrafica($tipo, $labels, $data, $label, $color, $bg, $horizontal = false): ?string
    {
        try {
            $config = [
                'type' => $tipo,
                'data' => [
                    'labels' => $labels->values()->all(),
                    'datasets' => [[
                        'label' => $label,
                        'data' => $data->values()->all(),
                        'borderColor' => $color,
                        'backgroundColor' => $bg,
                        'fill' => $tipo === 'line',
                        'tension' => 0.25,
                    ]],
                ],
                'options' => [
                    'indexAxis' => $horizontal ? 'y' : 'x',
                    'plugins' => ['legend' => ['display' => true]],
                ],
            ];

            $response = \Illuminate\Support\Facades\Http::timeout(10)->get('https://quickchart.io/chart', [
                'c' => json_encode($config),
                'width' => 600,
                'height' => 320,
                'backgroundColor' => 'white',
                'format' => 'png',
            ]);

            if ($response->successful()) {
                return 'data:image/png;base64,' . base64_encode($response->body());
            }
        } catch (\Throwable $e) {
            // Si QuickChart no responde (sin internet, timeout, etc.) devolvemos null y el blade cae al respaldo en tabla XD
        }
        return null;
    }

    /**
     * Genera la imagen de la dona (Estados) vía QuickChart.
     */
    private function generarImagenGraficaDona($labels, $data): ?string
    {
        try {
            $config = [
                'type' => 'doughnut',
                'data' => [
                    'labels' => $labels->values()->all(),
                    'datasets' => [[
                        'data' => $data->values()->all(),
                        'backgroundColor' => ['#f29436', '#36a2eb', '#e74a3b', '#28a745', '#ffc107', '#6c757d'],
                    ]],
                ],
                'options' => ['plugins' => ['legend' => ['display' => true, 'position' => 'right']]],
            ];

            $response = \Illuminate\Support\Facades\Http::timeout(10)->get('https://quickchart.io/chart', [
                'c' => json_encode($config),
                'width' => 500,
                'height' => 320,
                'backgroundColor' => 'white',
                'format' => 'png',
            ]);

            if ($response->successful()) {
                return 'data:image/png;base64,' . base64_encode($response->body());
            }
        } catch (\Throwable $e) {
            //
        }
        return null;
    }






}