<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Encapsula la interpretación de consultas en lenguaje natural mediante
 * un servicio externo de inteligencia artificial (actualmente Gemini).
 *
 * Esta clase NO ejecuta ninguna consulta a la base de datos: solo determina,
 * a partir del texto recibido, cuál función del catálogo de CU-22 corresponde
 * invocar y con qué parámetros. La ejecución real y la verificación de
 * permisos ocurren en el controller que use este servicio.
 */
class ServicioInterpretacionIA
{
    private const MODELO = 'gemini-3.5-flash';
    private const ENDPOINT = 'https://generativelanguage.googleapis.com/v1beta/models/' . self::MODELO . ':generateContent';

    private array $funciones;

    public function __construct()
    {
        $this->funciones = json_decode(
            file_get_contents(__DIR__ . '/function_declarations.json'),
            associative: true
        );
    }

    /**
     * Interpreta el texto transcrito de la consulta hablada y determina
     * qué función del catálogo corresponde ejecutar.
     *
     * @return array{nombre: string, parametros: array}|null  null si la IA no logró determinar una función válida.
     */
    public function interpretar(string $textoConsulta): ?array
    {
        $respuesta = Http::withHeaders([
            'x-goog-api-key' => config('services.gemini.api_key'),
            'Content-Type'   => 'application/json',
        ])->post(self::ENDPOINT, [
            'contents' => [
                [
                    'role'  => 'user',
                    'parts' => [
                        ['text' => $textoConsulta],
                    ],
                ],
            ],
            'tools' => [$this->funciones],
            'tool_config' => [
                'function_calling_config' => [
                    'mode'                  => 'VALIDATED',
                    'allowed_function_names' => array_column($this->funciones['functionDeclarations'], 'name'),
                ],
            ],
        ]);

        if ($respuesta->failed()) {
            Log::error('ServicioInterpretacionIA: fallo al consultar Gemini', [
                'status' => $respuesta->status(),
                'body'   => $respuesta->body(),
            ]);
            return null;
        }

        $partes = $respuesta->json('candidates.0.content.parts', []);

        foreach ($partes as $parte) {
            if (isset($parte['functionCall'])) {
                return [
                    'nombre'     => $parte['functionCall']['name'],
                    'parametros' => $parte['functionCall']['args'] ?? [],
                ];
            }
        }

        return null;
    }
}
