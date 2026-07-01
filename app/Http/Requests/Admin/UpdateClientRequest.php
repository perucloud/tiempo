<?php

namespace App\Http\Requests\Admin;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
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
            'nombres' => ['required', 'string', 'max:255'],
            'apellidos' => ['nullable', 'string', 'max:255'],
            'telefono' => [
                'required',
                'string',
                'max:30',
                Rule::unique('clientes', 'telefono')
                    ->ignore($this->route('client'))
                    ->whereNull('deleted_at'),
            ],
            'email' => ['nullable', 'email', 'max:255'],
            'documento' => ['nullable', 'string', 'max:30'],
            'estado' => ['required', Rule::in(Cliente::ESTADOS)],
        ];
    }
}
