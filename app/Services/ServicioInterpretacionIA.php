<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Encapsula la interpretación de consultas en lenguaje natural mediante
 * un servicio externo de inteligencia artificial (actualmente Gemini).
 *
 * Esta clase NO ejecuta ninguna consulta a la base de datos: solo determina,
 * a partir del audio recibido, qué dijo el usuario y cuál función del
 * catálogo de CU-22 corresponde invocar y con qué parámetros. La ejecución
 * real y la verificación de permisos ocurren en el controller que use
 * este servicio.
 *
 * Nota técnica: la interpretación (audio -> texto + decisión de función)
 * y la síntesis de voz de la respuesta (texto -> audio) son dos llamadas
 * distintas a la API de Gemini, porque requieren modelos diferentes
 * (el modelo de propósito general no genera audio de salida; el modelo
 * de TTS no hace function calling). Ambas siguen siendo el mismo
 * proveedor externo desde la perspectiva del sistema.
 */
class ServicioInterpretacionIA
{
    private const MODELO_INTERPRETACION = 'gemini-2.5-flash';
    private const MODELO_TTS = 'gemini-3.1-flash-tts-preview';

    private const ENDPOINT_INTERPRETACION = 'https://generativelanguage.googleapis.com/v1beta/models/' . self::MODELO_INTERPRETACION . ':generateContent';
    private const ENDPOINT_TTS = 'https://generativelanguage.googleapis.com/v1beta/models/' . self::MODELO_TTS . ':generateContent';

    private array $funciones;

    public function __construct()
    {
        $this->funciones = json_decode(
            file_get_contents(__DIR__ . '/function_declarations.json'),
            associative: true
        );

        // PHP no distingue entre un array vacío [] y un objeto vacío {} al
        // decodificar JSON con associative: true. Esto rompe a Gemini cuando
        // una función no tiene parámetros (ej. buscarTiposTrabajadorCatalogo),
        // porque 'properties' => [] se vuelve a serializar como lista, no
        // como mapa, y la API responde 400 "Cannot bind a list to map".
        // Se fuerza explícitamente cada 'properties' vacío a un objeto real.
        foreach ($this->funciones['functionDeclarations'] as &$funcion) {
            if (isset($funcion['parameters']['properties']) && empty($funcion['parameters']['properties'])) {
                $funcion['parameters']['properties'] = new \stdClass();
            }
        }
        unset($funcion);
    }

    /**
     * Interpreta el audio de la consulta hablada: lo transcribe y determina
     * qué función del catálogo corresponde ejecutar, en una sola llamada.
     *
     * @param string $audioBase64  Contenido del audio codificado en base64.
     * @param string $mimeType     Tipo MIME del audio (ej. 'audio/webm', 'audio/wav').
     * @return array{transcripcion: string, nombre: ?string, parametros: array, error: ?string}
     */
    public function interpretar(string $audioBase64, string $mimeType): array
    {
        $respuesta = Http::withHeaders([
            'x-goog-api-key' => config('services.gemini.api_key'),
            'Content-Type'   => 'application/json',
        ])
            // En Windows, PHP/cURL a menudo no encuentra el almacén de
            // certificados raíz por defecto, y la conexión se queda
            // esperando en vez de fallar con un error claro. Se le indica
            // a cURL que use el almacén nativo de certificados del sistema
            // operativo (disponible desde cURL 7.71 + PHP 8.2).
            ->withOptions([
                'curl' => [
                    CURLOPT_SSL_OPTIONS => defined('CURLSSLOPT_NATIVE_CA') ? CURLSSLOPT_NATIVE_CA : 0,
                ],
            ])
            ->timeout(20)
            // Gemini puede responder 503 "high demand" en picos puntuales de
            // tráfico — la propia API indica que suele ser temporal. Se
            // reintenta hasta 2 veces, con una pausa corta entre intentos,
            // antes de considerarlo un fallo real.
            ->retry(2, 1500, function ($exception, $request) {
                return $exception instanceof \Illuminate\Http\Client\RequestException
                    && $exception->response->status() === 503;
            }, throw: false)
            ->post(self::ENDPOINT_INTERPRETACION, [
            'contents' => [
                [
                    'role'  => 'user',
                    'parts' => [
                        ['text' => 'Transcribe textualmente lo que dice el audio y, en tu respuesta, incluye siempre esa transcripción como texto antes de decidir qué función del catálogo corresponde ejecutar. No omitas el texto transcrito aunque tengas clara la función a usar.'],
                        [
                            'inlineData' => [
                                'mimeType' => $mimeType,
                                'data'     => $audioBase64,
                            ],
                        ],
                    ],
                ],
            ],
            'tools' => [$this->funciones],
            'tool_config' => [
                'function_calling_config' => [
                    'mode'                   => 'VALIDATED',
                    'allowed_function_names' => array_column($this->funciones['functionDeclarations'], 'name'),
                ],
            ],
        ]);

        if ($respuesta->failed()) {
            Log::error('ServicioInterpretacionIA: fallo al consultar Gemini (interpretación)', [
                'status' => $respuesta->status(),
                'body'   => $respuesta->body(),
            ]);

            $error = $respuesta->status() === 503
                ? 'servicio_saturado'
                : 'error_desconocido';

            return ['transcripcion' => '', 'nombre' => null, 'parametros' => [], 'error' => $error];
        }

        $partes = $respuesta->json('candidates.0.content.parts', []);

        $transcripcion = '';
        $nombreFuncion = null;
        $parametros = [];

        foreach ($partes as $parte) {
            if (isset($parte['text'])) {
                $transcripcion .= $parte['text'];
            }
            if (isset($parte['functionCall'])) {
                $nombreFuncion = $parte['functionCall']['name'];
                $parametros = $parte['functionCall']['args'] ?? [];
            }
        }

        return [
            'transcripcion' => trim($transcripcion),
            'nombre'        => $nombreFuncion,
            'parametros'    => $parametros,
            'error'         => null,
        ];
    }

    /**
     * Redacta, en lenguaje natural, un resumen hablable del resultado de una
     * consulta de voz exitosa (CU-22), para usarlo como mensaje de éxito antes
     * de mandarlo a TTS. Llamada de texto puro a Gemini (sin function calling,
     * sin audio) — distinta de interpretar() y de generarAudioRespuesta().
     *
     * Si esta llamada falla (503, timeout, lo que sea), se devuelve null y
     * el llamador debe usar un mensaje de respaldo: esta función nunca debe
     * romper el flujo de la consulta de voz, solo enriquecerlo cuando se puede.
     *
     * @param string $nombreFuncion Nombre de la función del catálogo ejecutada.
     * @param array  $parametros    Parámetros con los que se ejecutó.
     * @param mixed  $resultado     Resultado real devuelto por el controller.
     */
    public function redactarResumen(string $nombreFuncion, array $parametros, mixed $resultado): ?string
    {
        // Normaliza Collections/modelos Eloquent a array PHP puro, igual que
        // se hace en ReporteController::exportarReporte().
        $resultadoPlano = json_decode(json_encode($resultado), true);

        // Si el resultado es una lista larga —ya sea el array raíz, o una
        // lista anidada dentro de una clave (patrón muy común en este
        // catálogo: {cantidad: N, proformas: [...]})—, no tiene sentido
        // (ni cabe bien en el prompt) mandarla completa: se manda el
        // conteo total más una muestra acotada de 5, suficiente para
        // redactar un resumen natural sin gastar tokens de más ni
        // arriesgar timeouts con resultados grandes.
        $totalRegistros = null;

        if (is_array($resultadoPlano) && array_is_list($resultadoPlano)) {
            $totalRegistros = count($resultadoPlano);
            $resultadoPlano = array_slice($resultadoPlano, 0, 5);
        } elseif (is_array($resultadoPlano)) {
            foreach ($resultadoPlano as $clave => $valor) {
                if (is_array($valor) && array_is_list($valor) && count($valor) > 5) {
                    $totalRegistros = $resultadoPlano['cantidad'] ?? count($valor);
                    $resultadoPlano[$clave] = array_slice($valor, 0, 5);
                }
            }
        }

        $contexto = json_encode([
            'funcion_consultada' => $nombreFuncion,
            'parametros_usados'  => $parametros,
            'total_registros'    => $totalRegistros,
            'muestra_resultado'  => $resultadoPlano,
        ], JSON_UNESCAPED_UNICODE);

        $respuesta = Http::withHeaders([
            'x-goog-api-key' => config('services.gemini.api_key'),
            'Content-Type'   => 'application/json',
        ])
            ->withOptions([
                'curl' => [
                    CURLOPT_SSL_OPTIONS => defined('CURLSSLOPT_NATIVE_CA') ? CURLSSLOPT_NATIVE_CA : 0,
                ],
            ])
            ->timeout(12    )
            ->retry(2, 800, function ($exception, $request) {
                return $exception instanceof \Illuminate\Http\Client\RequestException
                    && $exception->response->status() === 503;
            }, throw: false)
            ->post(self::ENDPOINT_INTERPRETACION, [
                'contents' => [
                    [
                        'role'  => 'user',
                        'parts' => [
                            ['text' =>
                                "Eres el asistente de voz de un taller automotriz. " .
                                "A continuación recibes, en JSON, el resultado de una consulta " .
                                "que un mecánico o administrador acaba de hacer por voz. " .
                                "Redacta una sola respuesta hablada, breve (máximo 2 oraciones), " .
                                "natural y en español, que resuma ese resultado como si se lo " .
                                "estuvieras diciendo en voz alta a la persona. " .
                                "Si 'total_registros' no es null, menciona la cantidad total, " .
                                "no solo la muestra. No leas el JSON ni menciones nombres de " .
                                "campos técnicos. No agregues saludos ni texto fuera de la respuesta.\n\n" .
                                "Resultado:\n{$contexto}"
                            ],
                        ],
                    ],
                ],
            ]);

        if ($respuesta->failed()) {
            Log::error('ServicioInterpretacionIA: fallo al consultar Gemini (resumen)', [
                'status' => $respuesta->status(),
                'body'   => $respuesta->body(),
            ]);
            return null;
        }

        $texto = trim($respuesta->json('candidates.0.content.parts.0.text', ''));

        return $texto !== '' ? $texto : null;
    }

    /**
     * Convierte un texto de respuesta en audio hablado, usando el modelo
     * de texto-a-voz de Gemini. Llamada separada de interpretar(), ya que
     * requiere un modelo distinto.
     *
     * @return array{audioBase64: string, mimeType: string}|null
     */
    public function generarAudioRespuesta(string $texto): ?array
    {
        $respuesta = Http::withHeaders([
            'x-goog-api-key' => config('services.gemini.api_key'),
            'Content-Type'   => 'application/json',
        ])
            ->withOptions([
                'curl' => [
                    CURLOPT_SSL_OPTIONS => defined('CURLSSLOPT_NATIVE_CA') ? CURLSSLOPT_NATIVE_CA : 0,
                ],
            ])
            ->timeout(20)
            ->post(self::ENDPOINT_TTS, [
            'contents' => [
                [
                    'role'  => 'user',
                    'parts' => [
                        ['text' => $texto],
                    ],
                ],
            ],
            'generationConfig' => [
                'responseModalities' => ['AUDIO'],
                'speechConfig' => [
                    'voiceConfig' => [
                        'prebuiltVoiceConfig' => ['voiceName' => 'Kore'],
                    ],
                ],
            ],
        ]);

        if ($respuesta->failed()) {
            Log::error('ServicioInterpretacionIA: fallo al consultar Gemini (TTS)', [
                'status' => $respuesta->status(),
                'body'   => $respuesta->body(),
            ]);
            return null;
        }

        $parteAudio = $respuesta->json('candidates.0.content.parts.0.inlineData');

        if (!$parteAudio) {
            return null;
        }

        // Gemini TTS devuelve audio PCM crudo (sin encabezado), no un WAV
        // reproducible directamente. El mimeType típico es
        // "audio/L16;codec=pcm;rate=24000". Se construye el encabezado WAV
        // alrededor de esos bytes para que el navegador pueda reproducirlo.
        $pcmCrudo  = base64_decode($parteAudio['data']);
        $sampleRate = $this->extraerSampleRate($parteAudio['mimeType'] ?? '');
        $wav        = $this->pcmAWav($pcmCrudo, $sampleRate);

        return [
            'audioBase64' => base64_encode($wav),
            'mimeType'    => 'audio/wav',
        ];
    }

    private function extraerSampleRate(string $mimeType): int
    {
        if (preg_match('/rate=(\d+)/', $mimeType, $coincidencia)) {
            return (int) $coincidencia[1];
        }
        return 24000; // valor por defecto documentado de Gemini TTS
    }

    /**
     * Envuelve datos PCM de 16 bits mono en un encabezado WAV estándar de 44 bytes,
     * siguiendo la estructura RIFF/WAVE documentada para la salida de Gemini TTS.
     */
    private function pcmAWav(string $pcmData, int $sampleRate, int $canales = 1, int $bitsPorMuestra = 16): string
    {
        $byteRate   = $sampleRate * $canales * $bitsPorMuestra / 8;
        $blockAlign = $canales * $bitsPorMuestra / 8;
        $dataSize   = strlen($pcmData);

        $cabecera  = 'RIFF';
        $cabecera .= pack('V', 36 + $dataSize);
        $cabecera .= 'WAVE';
        $cabecera .= 'fmt ';
        $cabecera .= pack('V', 16);
        $cabecera .= pack('v', 1); // PCM
        $cabecera .= pack('v', $canales);
        $cabecera .= pack('V', $sampleRate);
        $cabecera .= pack('V', $byteRate);
        $cabecera .= pack('v', $blockAlign);
        $cabecera .= pack('v', $bitsPorMuestra);
        $cabecera .= 'data';
        $cabecera .= pack('V', $dataSize);

        return $cabecera . $pcmData;
    }
}