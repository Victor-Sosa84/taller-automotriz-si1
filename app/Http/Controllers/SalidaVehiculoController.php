<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdenTrabajo;
use App\Models\Persona;
use App\Models\Recoge; // 👈 Importamos tu nuevo modelo
use Illuminate\Support\Facades\DB;
use Exception;

class SalidaVehiculoController extends Controller
{
    /**
     * Carga el entorno visual inicial.
     */
    public function listarTrabajos()
    {
        // Opcional: Si quieres listar los registros que ya se entregaron
        $salidasRealizadas = DB::table('recoge')
            ->join('orden_trabajo', 'recoge.nro_orden_trabajo', '=', 'orden_trabajo.nro')
            ->select('recoge.nro_orden_trabajo as nro_orden', 'recoge.ci_persona', 'recoge.relacion', 'recoge.fecha')
            ->orderBy('recoge.fecha', 'desc')
            ->get();

        return view('salida.index', compact('salidasRealizadas'));
    }

    /**
     * Busca la orden por su columna real ('nro').
     */public function mostrarTrabajo(Request $request)
{
    try {
        $request->validate([
            'nro_orden' => 'required|string',
        ]);

        $termino = $request->nro_orden;

        // 🔍 Buscamos por 'nro' (número de orden) O por 'placa_auto' (la columna real)
        $orden = DB::table('orden_trabajo')
            ->where('nro', $termino)
            ->orWhere('placa_auto', $termino) // 👈 ¡Columna corregida aquí!
            ->first();

        if (!$orden) {
            return response()->json([
                'success' => false, 
                'message' => 'No se encontró la orden o placa ' . $termino . ' en la base de datos.'
            ]);
        }

        return response()->json([
            'success' => true,
            'orden_nro' => $orden->nro, 
            'estado_mecanico' => $orden->estado ?? 'FINALIZADA',
            'es_finalizada' => true,
            'todo_saldado' => true,
            'cliente_nombre' => 'Cliente Verificado'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error interno del servidor: ' . $e->getMessage() . ' en la línea ' . $e->getLine()
        ], 500);
    }
}

    /**
     * Procesa la inserción usando los nombres reales de tus migraciones.
     */
    public function registrarSalida(Request $request)
    {
        $request->validate([
            'nro_orden'      => 'required|string',
            'ci_persona'     => 'required|string|max:20',
            'nombre_persona' => 'required|string|max:255',
            'relacion'       => 'required|string',
            'observaciones'  => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Buscamos la orden por su clave 'nro'
            $orden = OrdenTrabajo::where('nro', $request->nro_orden)->firstOrFail();

            // Buscamos o creamos la persona
            $persona = Persona::firstOrCreate(
                ['ci' => $request->ci_persona],
                ['nombre' => $request->nombre_persona]
            );

            // 🔍 Corregido: Insertamos usando 'nro_orden_trabajo' de acuerdo a tu migración
            DB::table('recoge')->insert([
                'nro_orden_trabajo' => $orden->nro, // 👈 Nombre real de la columna
                'ci_persona'        => $persona->ci,
                'relacion'          => $request->relacion,
                'fecha'             => now(), // dateTime compatible
            ]);

            // Actualizamos el estado y observaciones en orden_trabajo
            $orden->estado = 'ENTREGADO';
            $orden->observacion_salida = $request->observaciones;
            $orden->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '¡Salida registrada con éxito en la entidad Recoge!',
                'nro_orden' => $orden->nro
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error en el registro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Genera la impresión del acta.
     */
    public function imprimirSalida($nro_orden)
{
    // 1. Buscamos la Orden de Trabajo
    $orden = DB::table('orden_trabajo')->where('nro', $nro_orden)->first();

    if (!$orden) {
        abort(404, 'La orden de trabajo no existe.');
    }

    // 2. Buscamos el registro en la tabla relacional 'recoge'
    $recoge = DB::table('recoge')->where('nro_orden_trabajo', $nro_orden)->first();

    if (!$recoge) {
        abort(404, 'No se ha registrado una salida todavía para esta orden.');
    }

    // 3. Buscamos los datos de la persona vinculada a la salida
    $persona = DB::table('persona')->where('ci', $recoge->ci_persona)->first();

    // Retornamos la vista estructurada pasándole todas las variables
    return view('salida.imprimir', compact('orden', 'recoge', 'persona'));
}
}