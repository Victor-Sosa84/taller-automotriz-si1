<?php
// app/Models/ProformaRepuesto.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProformaRepuesto extends Model
{
    protected $table    = 'proforma_repuesto';
    public    $timestamps = false;
    protected $fillable = ['nro_proforma', 'id_repuesto', 'cantidad', 'precio_unitario', 'descuento'];

    public function repuesto()
    {
        return $this->belongsTo(Repuesto::class, 'id_repuesto', 'id');
    }
}
