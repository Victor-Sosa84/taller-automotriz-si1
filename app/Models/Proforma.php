<?php
// ── app/Models/Proforma.php ──────────────────────────────────
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Proforma extends Model
{
    protected $table      = 'proforma';
    protected $primaryKey = 'nro';
    public    $timestamps = false;

    protected $fillable = ['ci_cliente', 'id_diagnostico', 'fecha', 'total_aprox', 'estado', 'plazo'];

    protected $casts = ['fecha' => 'datetime'];

    public function diagnostico()
    {
        return $this->belongsTo(Diagnostico::class, 'id_diagnostico', 'id');
    }

    public function cliente()
    {
        return $this->belongsTo(Persona::class, 'ci_cliente', 'ci');
    }

    public function ordenTrabajo()
    {
        return $this->hasOne(OrdenTrabajo::class, 'nro_proforma', 'nro');
    }

    public function repuestos()
    {
        return $this->hasMany(ProformaRepuesto::class, 'nro_proforma', 'nro');
    }

    public function servicios()
    {
        return $this->hasMany(ProformaServicio::class, 'nro_proforma', 'nro');
    }

    public function calcularTotal(): float
    {
        $totalRepuestos = $this->repuestos->sum(function ($r) {
            return ($r->precio_unitario * $r->cantidad) * (1 - $r->descuento / 100);
        });
        $totalServicios = $this->servicios->sum(function ($s) {
            return $s->costo * $s->cantidad;
        });
        return round($totalRepuestos + $totalServicios, 2);
    }

    public function getEstadoVisualAttribute()
    {
        if (in_array($this->estado, ['Emitida', 'Observada']) 
            && $this->plazo 
            && now()->toDateString() > $this->plazo) {
            return 'Vencida';
        }
        return $this->estado;
    }
}
