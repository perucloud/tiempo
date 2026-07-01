<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreDeliveryZoneRequest extends FormRequest
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
            'nombre' => ['required', 'string', 'max:120'],
            'descripcion_cobertura' => ['nullable', 'string', 'max:500'],
            'costo_delivery' => ['required', 'numeric', 'min:0', 'max:9999'],
            'pedido_minimo' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'activo' => ['nullable', 'boolean'],
        ];
    }
}
