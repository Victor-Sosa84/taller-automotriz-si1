<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleTrabajo extends Model
{
    protected $table    = 'detalle_trabajo';
    public    $timestamps = false;
    protected $fillable = ['nro_orden_trabajo', 'id_mano_obra', 'costo', 'estado', 'cantidad'];

    public function manoObra()
    {
        return $this->belongsTo(ManoObra::class, 'id_mano_obra', 'id');
    }

    public function ordenTrabajo()
    {
        return $this->belongsTo(OrdenTrabajo::class, 'nro_orden_trabajo', 'nro');
    }
}
