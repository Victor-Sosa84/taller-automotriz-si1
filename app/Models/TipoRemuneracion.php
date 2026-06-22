<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoRemuneracion extends Model
{
    protected $table = 'tipo_remuneracion';
    protected $primaryKey = 'nro';
    public $timestamps = false;

    protected $fillable = ['descripcion'];

    // Un tipo de remuneración puede estar en muchos contratos
    public function contratos()
    {
        return $this->hasMany(Contrato::class, 'tipo_remuneracion', 'nro');
    }
}
