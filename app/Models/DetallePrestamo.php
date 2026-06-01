<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DetallePrestamo extends Model
{
    protected $table = 'detalle_prestamo';
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = ['id_prestamo_herramienta', 'nro_herramienta', 'estado_salida', 'estado_retorno'];

    public function herramienta()
    {
        return $this->belongsTo(Herramienta::class, 'nro_herramienta', 'nro');
    }

    public function prestamo()
    {
        return $this->belongsTo(PrestamoHerramienta::class, 'id_prestamo_herramienta', 'id');
    }
}