<?php

namespace App\Http\Requests\Admin;

use App\Models\Repartidor;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourierRequest extends FormRequest
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
            'nombres' => ['required', 'string', 'max:120'],
            'apellidos' => ['nullable', 'string', 'max:120'],
            'telefono' => [
                'required',
                'string',
                'max:30',
                Rule::unique('repartidores', 'telefono')->whereNull('deleted_at'),
            ],
            'documento' => ['nullable', 'string', 'max:30'],
            'vehiculo_tipo' => ['nullable', 'string', 'max:60'],
            'vehiculo_placa' => ['nullable', 'string', 'max:30'],
            'estado' => ['required', Rule::in(Repartidor::ESTADOS)],
        ];
    }
}
