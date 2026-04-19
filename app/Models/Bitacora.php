<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table    = 'bitacora';
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

    // ── Helper estático para registrar acciones fácilmente ───────
    public static function registrar(string $accion, int $idUsuario, ?string $ip = null): void
    {
        static::create([
            'id_usuario' => $idUsuario,
            'accion'     => $accion,
            'ip_equipo'  => $ip ?? request()->ip(),
            'fecha_hora' => now(),
        ]);
    }
}
