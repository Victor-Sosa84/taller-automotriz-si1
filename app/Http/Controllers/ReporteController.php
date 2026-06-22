<?php

namespace App\Http\Controllers;

use App\Services\ServicioInterpretacionIA;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function __construct(private ServicioInterpretacionIA $servicioIA)
    {
    }

    public function consultarReporte(Request $request)
    {
        $request->validate([
            'consulta' => ['required', 'string', 'max:500'],
        ]);

        $interpretacion = $this->servicioIA->interpretar($request->consulta);

        if (!$interpretacion) {
            return response()->json([
                'ok'      => false,
                'mensaje' => 'No pude entender la consulta. ¿Puedes reformularla?',
            ], 200);
        }

        $nombreFuncion = $interpretacion['nombre'];
        $parametros    = $interpretacion['parametros'];

        $catalogo = config('reporte_voz');

        // El nombre debe existir EXACTAMENTE en la whitelist, sin excepción.
        if (!array_key_exists($nombreFuncion, $catalogo)) {
            return response()->json([
                'ok'      => false,
                'mensaje' => 'Esa consulta no está disponible en el sistema.',
            ], 200);
        }

        $definicion = $catalogo[$nombreFuncion];

        // Verificación de permiso — el corazón de la seguridad de CU-22.
        // CU22_GEN solo habilita el comando de voz; este permiso autoriza el dato.
        if ($definicion['permiso'] && !auth()->user()->puede($definicion['permiso'])) {
            return response()->json([
                'ok'      => false,
                'mensaje' => 'No tienes permiso para consultar esa información.',
            ], 200);
        }

        $controller = app($definicion['controller']);
        $resultado  = call_user_func_array([$controller, $definicion['metodo']], $parametros);

        return response()->json([
            'ok'        => true,
            'funcion'   => $nombreFuncion,
            'resultado' => $resultado,
        ]);
    }
}
