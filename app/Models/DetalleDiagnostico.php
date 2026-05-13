<?php
// ── app/Models/DetalleDiagnostico.php ────────────────────────
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DetalleDiagnostico extends Model
{
    protected $table      = 'detalle_diagnostico';
    public    $timestamps = false;
    public    $incrementing = false;
    protected $primaryKey = 'id_diagnostico'; // cualquiera de las dos, solo para que no busque 'id'
    protected $fillable   = ['id_diagnostico', 'id_detalle_diagnostico', 'falla'];

    public function diagnostico()
    {
        return $this->belongsTo(Diagnostico::class, 'id_diagnostico', 'id');
    }
}