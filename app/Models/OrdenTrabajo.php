<?php
// ── app/Models/OrdenTrabajo.php ──────────────────────────────
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OrdenTrabajo extends Model
{
    protected $table      = 'orden_trabajo';
    protected $primaryKey = 'nro';
    public    $timestamps = false;

    protected $fillable = [
        'nro_proforma', 'fecha_inicio', 'fecha_fin',
        'estado', 'kilometraje', 'observacion_entrada', 'observacion_salida',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin'    => 'datetime',
    ];

    public function proforma()
    {
        return $this->belongsTo(Proforma::class, 'nro_proforma', 'nro');
    }

    public function detallesTrabajo()
    {
        return $this->hasMany(DetalleTrabajo::class, 'nro_orden_trabajo', 'nro');
    }

    public function detallesRepuesto()
    {
        return $this->hasMany(DetalleRepuesto::class, 'nro_orden_trabajo', 'nro');
    }
}
