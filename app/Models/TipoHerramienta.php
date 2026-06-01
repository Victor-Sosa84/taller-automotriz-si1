<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TipoHerramienta extends Model
{
    protected $table = 'tipo_herramienta';
    public $timestamps = false;
    protected $fillable = ['descripcion'];
}