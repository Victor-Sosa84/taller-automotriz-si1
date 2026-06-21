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

    public static function guardarFactura(OrdenTrabajo $orden, string $nit, string $nombre): self
    {
        $factura = self::create([
            'nro_orden_trabajo' => $orden->nro,
            'fecha_emision'     => now(),
            'nit'               => $nit,
            'nombre'            => $nombre,
            'total'             => $orden->total_real,
            'plazo'             => null,
        ]);

        $factura->guardarDetalle($orden->detallesRepuesto, $orden->detallesTrabajo);

        return $factura;
    }

    public function guardarDetalle($detallesRepuesto, $detallesTrabajo): void
    {
        $siguienteId = 1;

        foreach ($detallesRepuesto as $dr) {
            DetalleFactura::create([
                'nro_factura'     => $this->nro,
                'id'              => $siguienteId++,
                'descripcion'     => $dr->repuesto->nombre,
                'tipo'            => 'Repuesto',
                'cantidad'        => $dr->cantidad,
                'precio_unitario' => $dr->precio_unitario,
                'precio'          => $dr->cantidad * $dr->precio_unitario * (1 - $dr->descuento / 100),
            ]);
        }

        foreach ($detallesTrabajo as $dt) {
            DetalleFactura::create([
                'nro_factura'     => $this->nro,
                'id'              => $siguienteId++,
                'descripcion'     => $dt->manoObra->descripcion,
                'tipo'            => 'Mano de Obra',
                'cantidad'        => $dt->cantidad,
                'precio_unitario' => $dt->costo,
                'precio'          => $dt->cantidad * $dt->costo,
            ]);
        }
    }

    public function getSaldoPendienteAttribute()
    {
        return $this->total - $this->cuotas->sum('monto');
    }
}