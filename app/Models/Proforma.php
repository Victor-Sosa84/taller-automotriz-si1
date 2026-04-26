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
}
