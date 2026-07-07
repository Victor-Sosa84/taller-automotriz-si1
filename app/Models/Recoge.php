<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recoge extends Model
{
    protected $table = 'recoge';

    // Como no tiene una sola columna 'id' autoincremental, desactivamos el incremento
    protected $primaryKey = ['nro_orden_trabajo', 'ci_persona'];
    public $incrementing = false;
    protected $keyType = 'string';

    // Tu migración solo define 'fecha', no usa 'created_at' ni 'updated_at' por defecto
    public $timestamps = false;

    protected $fillable = [
        'nro_orden_trabajo',
        'ci_persona',
        'relacion',
        'fecha'
    ];

    // Relación con la Orden de Trabajo
    public function ordenTrabajo()
    {
        return $this->belongsTo(OrdenTrabajo::class, 'nro_orden_trabajo', 'nro');
    }

    // Relación con la Persona
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'ci_persona', 'ci');
    }
}