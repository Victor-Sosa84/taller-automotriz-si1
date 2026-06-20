<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $table = 'factura';
    protected $primaryKey = 'nro';
    public $timestamps = false;

    protected $fillable = [
        'nro_orden_trabajo',
        'fecha_emision',
        'nit',
        'nombre',
        'total',
        'plazo',
    ];

    protected $casts = [
        'fecha_emision' => 'datetime',
        'plazo' => 'date',
    ];

    public function ordenTrabajo()
    {
        return $this->belongsTo(OrdenTrabajo::class, 'nro_orden_trabajo', 'nro');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleFactura::class, 'nro_factura', 'nro');
    }

    public function cuotas()
    {
        return $this->hasMany(Cuota::class, 'nro_factura', 'nro');
    }
}