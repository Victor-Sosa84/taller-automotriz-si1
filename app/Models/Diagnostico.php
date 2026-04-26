<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnostico extends Model
{
    protected $table    = 'diagnostico';
    public    $timestamps = false;

    protected $fillable = [
        'fecha',
        'ci_personal',
        'placa_auto',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'ci_personal', 'ci');
    }

    public function auto()
    {
        return $this->belongsTo(Auto::class, 'placa_auto', 'placa');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleDiagnostico::class, 'id_diagnostico', 'id');
    }

    public function proforma()
    {
        return $this->hasOne(Proforma::class, 'id_diagnostico', 'id');
    }
}
