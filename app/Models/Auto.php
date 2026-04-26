<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auto extends Model
{
    protected $table      = 'auto';
    protected $primaryKey = 'placa';
    public    $keyType    = 'string';
    public    $incrementing = false;
    public    $timestamps   = false;

    protected $fillable = [
        'placa',
        'marca',
        'modelo',
        'anio',
        'color',
    ];

    // Un auto puede tener muchos diagnósticos
    public function diagnosticos()
    {
        return $this->hasMany(Diagnostico::class, 'placa_auto', 'placa');
    }

    // Últimos clientes que trajeron este auto (via diagnóstico)
    public function clientes()
    {
        return $this->hasManyThrough(
            Persona::class,
            Diagnostico::class,
            'placa_auto', // FK en diagnostico
            'ci',         // FK en persona
            'placa',      // PK local
            'ci_personal' // FK en diagnostico hacia persona
        );
    }

    // ¿Tiene diagnósticos? Usado para proteger el delete
    public function tieneDiagnosticos(): bool
    {
        return $this->diagnosticos()->exists();
    }
}
