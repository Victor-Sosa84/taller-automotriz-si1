<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoTrabajador extends Model
{
    protected $table    = 'tipo_trabajador';
    public    $timestamps = false;
    protected $fillable = ['descripcion'];

    // Personas que tienen este tipo de cargo
    public function personal()
    {
        return $this->belongsToMany(
            Persona::class,
            'adquiere',
            'id_tipo_trabajador',
            'ci_personal',
            'id',
            'ci'
        );
    }

    // ¿Tiene personal asignado? — protege contra eliminación
    public function tienePersonal(): bool
    {
        return $this->personal()->exists();
    }
}
