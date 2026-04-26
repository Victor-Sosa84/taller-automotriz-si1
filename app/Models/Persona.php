<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table      = 'persona';
    protected $primaryKey = 'ci';
    public    $keyType    = 'string';
    public    $incrementing = false;
    public    $timestamps   = false;

    protected $fillable = [
        'ci',
        'nombre',
        'telefono',
        'direccion',
        'es_cliente',
        'es_personal',
    ];

    protected $casts = [
        'es_cliente'   => 'boolean',
        'es_personal'  => 'boolean',
    ];

    // Una persona puede tener un usuario del sistema
    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'ci_personal', 'ci');
    }

    // Tipos de trabajo que tiene asignados (tabla adquiere)
    public function tiposTrabajador()
    {
        return $this->belongsToMany(
            TipoTrabajador::class,
            'adquiere',
            'ci_personal',
            'id_tipo_trabajador',
            'ci',
            'id'
        );
    }

}