<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $table      = 'usuario';
    protected $primaryKey = 'id_usuario';
    public    $timestamps = false;

    // Laravel Auth espera 'password' — mapeamos 'clave' mediante accessor/mutator
    protected $fillable = [
        'id_rol',
        'ci_personal',
        'nombre_usuario',
        'clave',
        'correo',
    ];

    protected $hidden = ['clave'];

    // ── Mapeo para Laravel Auth ──────────────────────────────────
    // Auth usa getAuthPassword() para comparar — lo apuntamos a 'clave'
    public function getAuthPassword(): string
    {
        return $this->clave;
    }

    // Auth usa getAuthIdentifierName() para saber qué columna es el login
    // Esto se configura en config/auth.php (ver comentario al final)

    // ── Relaciones ───────────────────────────────────────────────
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol');
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'ci_personal', 'ci');
    }

    public function bitacoras()
    {
        return $this->hasMany(Bitacora::class, 'id_usuario');
    }

    // ── Helpers de rol ───────────────────────────────────────────
    public function esAdmin(): bool
    {
        return $this->id_rol === 1;
    }

    public function esMecanico(): bool
    {
        return $this->id_rol === 2;
    }

    public function esRecepcionista(): bool
    {
        return $this->id_rol === 3;
    }

    public function getNombreRolAttribute(): string
    {
        return $this->rol?->nombre ?? 'Sin rol';
    }
}

/*
|--------------------------------------------------------------------------
| CONFIGURACIÓN REQUERIDA EN config/auth.php
|--------------------------------------------------------------------------
| Cambia el provider 'users' para que apunte a tu modelo y columna:
|
|  'providers' => [
|      'users' => [
|          'driver' => 'eloquent',
|          'model'  => App\Models\Usuario::class,
|      ],
|  ],
|
| Y en config/auth.php, guards.web:
|  'web' => [
|      'driver'   => 'session',
|      'provider' => 'users',
|  ],
|
| En tu login, el campo de identificación debe ser 'correo' (no 'email').
| Si tu LoginController usa Auth::attempt(), pásale:
|   ['correo' => $request->correo, 'password' => $request->clave]
| Laravel internamente llama getAuthPassword() que devuelve $this->clave.
*/
