<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // 1. Dile a Laravel que use TU tabla
    protected $table = 'usuario';

    // 2. Tu llave primaria es id_usuario
    protected $primaryKey = 'id_usuario';

    // 3. Desactivamos los timestamps si tu tabla no tiene created_at/updated_at
    public $timestamps = false;

    /**
     * Atributos que se pueden asignar masivamente.
     * Ajustados a los nombres de tu tabla 'usuario'.
     */
    protected $fillable = [
        'nombre_usuario',
        'correo',
        'clave',
        'id_rol',
        'ci_personal',
    ];

    /**
     * Atributos ocultos (para que no se vean en respuestas JSON).
     */
    protected $hidden = [
        'clave',
        'remember_token',
    ];

    /**
     * IMPORTANTE: Laravel busca por defecto la columna 'password'.
     * Con esta función le decimos que en tu tabla se llama 'clave'.
     */
    public function getAuthPassword()
    {
        return $this->clave;
    }

    /**
     * Mapeo de casts.
     */
    protected function casts(): array
    {
        return [
            // Le decimos que 'clave' es un campo hasheado
            'clave' => 'hashed',
        ];
    }
}
