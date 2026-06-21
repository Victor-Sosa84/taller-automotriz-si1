<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleFactura extends Model
{
    protected $table = 'detalle_factura';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nro_factura',
        'id',
        'descripcion',
        'tipo',
        'cantidad',
        'precio',
        'precio_unitario',
        'descuento'
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'nro_factura', 'nro');
    }
}