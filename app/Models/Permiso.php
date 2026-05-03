<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    protected $table    = 'permiso';
    public    $timestamps = false;

    protected $fillable = ['nombre', 'etiqueta', 'modulo'];

    public function roles()
    {
        return $this->belongsToMany(
            Rol::class,
            'rol_permiso',
            'id_permiso',
            'id_rol'
        )->withPivot('estado', 'fecha_registro', 'observaciones');
    }
}
