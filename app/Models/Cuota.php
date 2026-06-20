<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuota extends Model
{
    protected $table = 'cuota';
    protected $primaryKey = 'nro';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nro_factura',
        'nro',
        'monto',
        'fecha',
        'tipo_pago',
        'referencia_stripe',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'nro_factura', 'nro');
    }
}