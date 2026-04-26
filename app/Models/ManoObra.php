<?php
// ── app/Models/ManoObra.php ──────────────────────────────────
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ManoObra extends Model
{
    protected $table    = 'mano_obra';
    public    $timestamps = false;
    protected $fillable = ['descripcion'];
}
