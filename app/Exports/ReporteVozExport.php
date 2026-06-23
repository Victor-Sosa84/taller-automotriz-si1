<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Convierte el resultado de cualquiera de las funciones del catálogo de
 * CU-22 (cada una con una forma de datos distinta) en filas exportables.
 *
 * Si el resultado contiene una lista anidada (ej. ['cantidad' => 3,
 * 'facturas' => [...]]), se exporta esa lista, ya que es la información
 * de detalle que tiene sentido ver fila por fila. Si el resultado es un
 * array plano de valores simples, se exporta como una sola fila.
 */
class ReporteVozExport implements FromCollection, WithHeadings
{
    private array $filas = [];
    private array $columnas = [];

    public function __construct(mixed $resultado)
    {
        $this->prepararFilas($resultado);
    }

    private function prepararFilas(mixed $resultado): void
    {
        if (!is_array($resultado)) {
            $this->columnas = ['valor'];
            $this->filas = [[$resultado]];
            return;
        }

        // Busca la primera clave cuyo valor sea una lista de filas (array de arrays/objetos).
        foreach ($resultado as $valor) {
            if (is_array($valor) && isset($valor[0]) && is_array($valor[0])) {
                $this->columnas = array_keys($valor[0]);
                $this->filas = array_map(fn ($fila) => array_values($fila), $valor);
                return;
            }
        }

        // No hay lista anidada: se exporta el resultado como una sola fila clave-valor.
        $this->columnas = array_keys($resultado);
        $this->filas = [array_map(
            fn ($v) => is_array($v) ? implode(', ', $v) : $v,
            array_values($resultado)
        )];
    }

    public function collection()
    {
        return new Collection($this->filas);
    }

    public function headings(): array
    {
        return $this->columnas;
    }
}
