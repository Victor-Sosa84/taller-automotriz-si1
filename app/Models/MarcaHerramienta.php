<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MarcaHerramienta extends Model
{
    protected $table = 'marca_herramienta';
    public $timestamps = false;
    protected $fillable = ['nombre'];
}