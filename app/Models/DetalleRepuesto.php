<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleRepuesto extends Model
{
    protected $table    = 'detalle_repuesto';
    public    $timestamps = false;
    protected $fillable = ['nro_orden_trabajo', 'id_repuesto', 'cantidad', 'precio_unitario', 'descuento'];

    public function repuesto()
    {
        return $this->belongsTo(Repuesto::class, 'id_repuesto', 'id');
    }
}
