<?php

namespace App\Http\Requests\Admin;

use App\Models\Pedido;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, [
            User::ROLE_SUPERADMIN,
            User::ROLE_ADMIN,
            User::ROLE_OPERADOR,
        ], true);
    }

    public function rules(): array
    {
        return [
            'estado' => ['required', Rule::in(Pedido::ESTADOS)],
            'comentario' => ['nullable', 'string', 'max:500'],
        ];
    }
}
