<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repuesto extends Model
{
    protected $table    = 'repuesto';
    public    $timestamps = false;
    protected $fillable = ['nombre', 'estado', 'marca'];
}
