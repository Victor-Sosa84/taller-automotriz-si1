<?php
// ── app/Models/DetalleDiagnostico.php ────────────────────────
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DetalleDiagnostico extends Model
{
    protected $table    = 'detalle_diagnostico';
    public    $timestamps = false;
    protected $fillable = ['id_diagnostico', 'id_detalle_diagnostico', 'descripcion'];

    public function diagnostico()
    {
        return $this->belongsTo(Diagnostico::class, 'id_diagnostico', 'id');
    }
}
