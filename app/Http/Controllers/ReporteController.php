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
            'audio'     => ['required', 'file', 'max:10240'], // máx ~10MB
            'mime_type' => ['required', 'string'],
        ]);

        $audioBase64 = base64_encode(file_get_contents($request->file('audio')->getRealPath()));

        $interpretacion = $this->servicioIA->interpretar($audioBase64, $request->mime_type);

        if ($interpretacion['error'] === 'servicio_saturado') {
            return $this->responderConAudio(false, 'El servicio de inteligencia artificial está temporalmente saturado. Por favor, intenta de nuevo en unos segundos.');
        }

        if ($interpretacion['error']) {
            return $this->responderConAudio(false, 'No pude procesar el audio. ¿Puedes intentarlo de nuevo?');
        }

        $transcripcion = $interpretacion['transcripcion'];
        $nombreFuncion = $interpretacion['nombre'];
        $parametros    = $interpretacion['parametros'];

        if (!$nombreFuncion) {
            return $this->responderConAudio(false, 'No logré entender qué información necesitas. ¿Puedes reformular la pregunta?', $transcripcion);
        }

        $catalogo = config('reporte_voz');

        // El nombre debe existir EXACTAMENTE en la whitelist, sin excepción.
        if (!array_key_exists($nombreFuncion, $catalogo)) {
            return $this->responderConAudio(false, 'Esa consulta no está disponible en el sistema.', $transcripcion);
        }

        $definicion = $catalogo[$nombreFuncion];

        // Verificación de permiso — el corazón de la seguridad de CU-22.
        // CU22_GEN solo habilita el comando de voz; este permiso autoriza el dato.
        if ($definicion['permiso'] && !auth()->user()->puede($definicion['permiso'])) {
            return $this->responderConAudio(false, 'No tienes permiso para consultar esa información.', $transcripcion);
        }

        $controller = app($definicion['controller']);
        $resultado  = call_user_func_array([$controller, $definicion['metodo']], $parametros);

        return $this->responderConAudio(true, 'Aquí está el resultado de tu consulta.', $transcripcion, $nombreFuncion, $resultado, $parametros);
    }

    /**
     * Re-ejecuta la última consulta de voz (identificada por función + parámetros,
     * nunca por datos ya mostrados en pantalla) y exporta el resultado fresco
     * en el formato solicitado. Vuelve a verificar el permiso correspondiente,
     * igual que consultarReporte(), para que el archivo exportado nunca pueda
     * construirse a partir de datos manipulados del lado del navegador.
     */
    public function exportarReporte(Request $request)
    {
        $request->validate([
            'funcion'   => ['required', 'string'],
            'parametros' => ['array'],
            'formato'   => ['required', 'in:pdf,excel'],
        ]);

        $catalogo = config('reporte_voz');
        $nombreFuncion = $request->funcion;
        $parametros    = $request->input('parametros', []);

        if (!array_key_exists($nombreFuncion, $catalogo)) {
            abort(404, 'Esa consulta no está disponible en el sistema.');
        }

        $definicion = $catalogo[$nombreFuncion];

        if ($definicion['permiso'] && !auth()->user()->puede($definicion['permiso'])) {
            abort(403, 'No tienes permiso para exportar esa información.');
        }

        $controller = app($definicion['controller']);
        $resultado  = call_user_func_array([$controller, $definicion['metodo']], $parametros);

        // Normaliza Collections/modelos Eloquent a array PHP puro antes de
        // exportar, igual que se hace en ReporteVozExport.
        $resultado = json_decode(json_encode($resultado), true);

        if ($request->formato === 'pdf') {
            return \PDF::loadView('reporte_voz.export_pdf', [
                'funcion'   => $nombreFuncion,
                'resultado' => $resultado,
            ])->download('reporte-' . $nombreFuncion . '.pdf');
        }

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ReporteVozExport($resultado),
            'reporte-' . $nombreFuncion . '.xlsx'
        );
    }

    /**
     * Construye la respuesta JSON y, además, el audio hablado correspondiente
     * mediante el modelo de TTS, antes de devolver todo junto al frontend.
     */
    private function responderConAudio(
        bool $ok,
        string $mensajeHablado,
        ?string $transcripcion = null,
        ?string $funcion = null,
        mixed $resultado = null,
        array $parametros = []
    ) {
        $audio = $this->servicioIA->generarAudioRespuesta($mensajeHablado);

        return response()->json([
            'ok'             => $ok,
            'transcripcion'  => $transcripcion,
            'mensaje'        => $mensajeHablado,
            'funcion'        => $funcion,
            'parametros'     => $parametros,
            'resultado'      => $resultado,
            'audio_base64'   => $audio['audioBase64'] ?? null,
            'audio_mime'     => $audio['mimeType'] ?? null,
        ]);
    }
}