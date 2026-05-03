<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Bitacora extends Model
{
    protected $table      = 'bitacora';
    public    $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'accion',
        'ip_equipo',
        'fecha_hora',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Registra una acción en la bitácora.
     *
     * Uso:
     *   Bitacora::registrar('Registro de Cliente', 'CI: 8512347 — Juan Pérez');
     *   Bitacora::registrar('Edición de Vehículo', 'Placa: ABC-1234');
     *   Bitacora::registrar('Eliminación de Usuario', 'usuario: mprueba');
     *
     * @param string      $accion   Tipo de acción (max 50 chars)
     * @param string|null $detalle  Detalle adicional — se concatena a la acción
     * @param int|null    $idUsuario Si null, usa el usuario autenticado
     */
    public static function registrar(
        string  $accion,
        ?string $detalle   = null,
        ?int    $idUsuario = null,
        ?string $ip        = null
    ): void {
        $id = $idUsuario ?? Auth::user()?->id_usuario;

        if (!$id) return; // no registrar si no hay sesión

        $accionCompleta = $detalle
            ? "{$accion} — {$detalle}"
            : $accion;

        static::create([
            'id_usuario' => $id,
            'accion'     => $accionCompleta,
            'ip_equipo'  => $ip ?? request()->ip(),
            'fecha_hora' => now(),
        ]);
    }
}