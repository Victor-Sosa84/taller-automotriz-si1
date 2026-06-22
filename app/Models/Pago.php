<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pago';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_contrato',
        'fecha_pago',
        'monto',
        'tipo',
        'metodo'
    ];

    protected $casts = [
        'fecha_pago' => 'datetime',
    ];

    // El pago se asocia a un contrato específico
    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'id_contrato', 'id');
    }
}