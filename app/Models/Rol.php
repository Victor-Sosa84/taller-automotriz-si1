<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table    = 'rol';
    public    $timestamps = false;

    protected $fillable = ['nombre', 'descripcion'];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_rol');
    }

    public function permisos()
    {
        return $this->belongsToMany(
            Permiso::class,
            'rol_permiso',
            'id_rol',
            'id_permiso'
        )->withPivot('estado', 'fecha_registro', 'observaciones');
    }

    // Solo permisos activos — usado por el middleware
    public function permisosActivos()
    {
        return $this->permisos()->wherePivot('estado', 'Activo');
    }

    // Verifica si el rol tiene un permiso activo
    public function tienePermiso(string $nombrePermiso): bool
    {
        return $this->permisosActivos()
                    ->where('nombre', $nombrePermiso)
                    ->exists();
    }
}
