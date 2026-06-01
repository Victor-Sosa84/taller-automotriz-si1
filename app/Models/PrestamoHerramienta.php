<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PrestamoHerramienta extends Model
{
    protected $table = 'prestamo_herramienta';
    public $timestamps = false;
    protected $fillable = ['fecha_salida', 'fecha_devolucion'];

    protected $casts = [
        'fecha_salida'     => 'datetime',
        'fecha_devolucion' => 'datetime',
    ];

    public function detalles()
    {
        return $this->hasMany(DetallePrestamo::class, 'id_prestamo_herramienta', 'id');
    }
}