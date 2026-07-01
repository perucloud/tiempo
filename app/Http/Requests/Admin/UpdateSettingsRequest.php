<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, [
            User::ROLE_SUPERADMIN,
            User::ROLE_ADMIN,
        ], true);
    }

    public function rules(): array
    {
        return [
            'settings' => ['required', 'array'],
            'settings.nombre_sistema' => ['required', 'string', 'max:120'],
            'settings.telefono_soporte' => ['nullable', 'string', 'max:40'],
            'settings.whatsapp_pedidos' => ['nullable', 'string', 'max:40'],
            'settings.email_contacto' => ['nullable', 'email', 'max:160'],
            'settings.direccion_base' => ['nullable', 'string', 'max:220'],
            'settings.horario_atencion' => ['nullable', 'string', 'max:180'],
            'settings.tarifa_base_delivery' => ['required', 'numeric', 'min:0', 'max:9999'],
        ];
    }
}
