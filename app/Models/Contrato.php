<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    protected $table = 'contrato';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'ci_personal',
        'tipo_remuneracion',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'periodo_pago',
        'valor'
    ];

    // El contrato pertenece a una persona (personal)
    public function personal()
    {
        return $this->belongsTo(Persona::class, 'ci_personal', 'ci');
    }

    // El contrato pertenece a un tipo de remuneración
    public function modalidadRemuneracion()
    {
        return $this->belongsTo(TipoRemuneracion::class, 'tipo_remuneracion', 'nro');
    }

    // Un contrato puede tener muchos pagos históricos registrados
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_contrato', 'id');
    }
}
