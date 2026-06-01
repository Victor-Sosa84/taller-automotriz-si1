<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Herramienta extends Model
{
    protected $table = 'herramienta';
    protected $primaryKey = 'nro';
    public $timestamps = false;
    protected $fillable = ['id_tipo_herramienta', 'id_marca_herramienta', 'descripcion', 'estado', 'disponible'];

    public function tipo()
    {
        return $this->belongsTo(TipoHerramienta::class, 'id_tipo_herramienta', 'id');
    }

    public function marca()
    {
        return $this->belongsTo(MarcaHerramienta::class, 'id_marca_herramienta', 'id');
    }
}