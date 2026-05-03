<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable; // Añade esto para que el correo pueda salir
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait; // Alias al Trait
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordInterface; // Alias a la Interfaz

class Usuario extends Authenticatable implements CanResetPasswordInterface
{
    use Notifiable, CanResetPasswordTrait; // Usamos el alias del trait aquí

    protected $table      = 'usuario';
    protected $primaryKey = 'id_usuario';
    public    $timestamps = false;

    // Laravel Auth espera 'password' — mapeamos 'clave' mediante accessor/mutator
    protected $fillable = [
        'id_rol',
        'ci_personal',
        'nombre_usuario',
        'clave',
        'correo',
    ];

    protected $hidden = ['clave'];

    // ── Mapeo para Laravel Auth ──────────────────────────────────
    // Auth usa getAuthPassword() para comparar — lo apuntamos a 'clave'
    public function getAuthPasswordName(): string
    {
        //return $this->clave;
        return 'clave';
    }

    public function setRememberToken($value) {}
    public function getRememberToken() { return null; }
    public function getRememberTokenName() { return ''; }

    // ── Mapeo para Password Reset ────────────────────────────────
    /**
     * Permite que Password::sendResetLink(['correo' => ...]) encuentre al usuario.
     * Laravel llama a este método en el provider para hacer la búsqueda.
     */
    public function getEmailForPasswordReset(): string
    {
        return $this->correo ?? '';
    }

    /**
     * Sobreescribe la notificación para que use $this->correo como destino.
     * Sin esto, Laravel intenta enviar a $this->email que no existe.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \Illuminate\Auth\Notifications\ResetPassword($token));
    }

    // Alias de 'correo' como 'email' — necesario para la notificación de Breeze
    public function getEmailAttribute(): string
    {
        return $this->correo ?? '';
    }

    // ── Relaciones ───────────────────────────────────────────────
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol');
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'ci_personal', 'ci');
    }

    public function bitacoras()
    {
        return $this->hasMany(Bitacora::class, 'id_usuario');
    }

    // ── Helpers de rol ───────────────────────────────────────────
    public function esAdmin(): bool
    {
        return (int) $this->id_rol === 1;
    }

    public function esMecanico(): bool
    {
        return (int) $this->id_rol === 2;
    }

    public function esRecepcionista(): bool
    {
        return (int) $this->id_rol === 3;
    }

    public function getNombreRolAttribute(): string
    {
        return $this->rol?->nombre ?? 'Sin rol';
    }
        // ── Helper de permisos ───────────────────────────────────────
    /**
     * Verifica si el usuario tiene un permiso activo.
     * Usa caché en memoria para no repetir queries en el mismo request.
     *
     * Uso en Blade:  @if(auth()->user()->puede('CLI_VIEW'))
     * Uso en PHP:    auth()->user()->puede('CLI_VIEW')
     */
    public function puede(string $permiso): bool
    {
        // El admin siempre puede todo
        if ($this->esAdmin()) return true;

        // Cachear permisos del rol en memoria durante el request
        if (!isset($this->_permisosCache)) {
            $this->_permisosCache = $this->rol
                ?->permisosActivos()
                ->pluck('nombre')
                ->toArray() ?? [];
        }

        return in_array($permiso, $this->_permisosCache);
    }
}