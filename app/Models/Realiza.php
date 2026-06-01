<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Realiza extends Model
{
    protected $table = 'realiza';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'ci_personal',
        'nro_orden_trabajo',
        'id_mano_obra',
        'tipo_participacion',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'ci_personal', 'ci');
    }

    public function ordenTrabajo()
    {
        return $this->belongsTo(OrdenTrabajo::class, 'nro_orden_trabajo', 'nro');
    }

    public function manoObra()
    {
        return $this->belongsTo(ManoObra::class, 'id_mano_obra', 'id');
    }
}