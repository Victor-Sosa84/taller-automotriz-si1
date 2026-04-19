<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transforma el recurso en un array.
     * Controla exactamente qué campos se exponen en las respuestas JSON.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this['id'] ?? $this->id,
            'name'       => $this['name'] ?? $this->name,
            'email'      => $this['email'] ?? $this->email,
            'phone'      => $this['phone'] ?? $this->phone ?? null,
            'dni'        => $this['dni'] ?? $this->dni ?? null,
            'role'       => $this['role'] ?? $this->role,
            'role_label' => $this->getRoleLabel($this['role'] ?? $this->role ?? ''),
            'active'     => $this['active'] ?? $this->active,
            'created_at' => $this['created_at'] ?? ($this->created_at ?? now())->format('d/m/Y'),
        ];
    }

    private function getRoleLabel(string $role): string
    {
        return match ($role) {
            'admin'         => 'Administrador',
            'mecanico'      => 'Mecánico',
            'recepcionista' => 'Recepcionista',
            'cliente'       => 'Cliente',
            default         => ucfirst($role),
        };
    }
}
