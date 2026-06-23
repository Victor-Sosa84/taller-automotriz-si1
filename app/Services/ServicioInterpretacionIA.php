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
    private const MODELO_INTERPRETACION = 'gemini-3.5-flash';
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
    }

    /**
     * Interpreta el audio de la consulta hablada: lo transcribe y determina
     * qué función del catálogo corresponde ejecutar, en una sola llamada.
     *
     * @param string $audioBase64  Contenido del audio codificado en base64.
     * @param string $mimeType     Tipo MIME del audio (ej. 'audio/webm', 'audio/wav').
     * @return array{transcripcion: string, nombre: ?string, parametros: array}|null
     */
    public function interpretar(string $audioBase64, string $mimeType): ?array
    {
        $respuesta = Http::withHeaders([
            'x-goog-api-key' => config('services.gemini.api_key'),
            'Content-Type'   => 'application/json',
        ])->post(self::ENDPOINT_INTERPRETACION, [
            'contents' => [
                [
                    'role'  => 'user',
                    'parts' => [
                        ['text' => 'Transcribe el audio y determina qué función del catálogo corresponde ejecutar según lo que la persona pidió.'],
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
            return null;
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
        ];
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
        ])->post(self::ENDPOINT_TTS, [
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

        return [
            'audioBase64' => $parteAudio['data'],
            'mimeType'    => $parteAudio['mimeType'] ?? 'audio/wav',
        ];
    }
}