<?php
// app/Models/ProformaServicio.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProformaServicio extends Model
{
    protected $table    = 'proforma_servicio';
    public    $timestamps = false;
    protected $fillable = ['nro_proforma', 'id_mano_obra', 'costo', 'estado', 'cantidad'];

    public function manoObra()
    {
        return $this->belongsTo(ManoObra::class, 'id_mano_obra', 'id');
    }
}