<?php

namespace App\Http\Requests\Admin;

use App\Models\Categoria;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
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
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categorias', 'nombre')
                    ->ignore($this->route('category'))
                    ->whereNull('deleted_at'),
            ],
            'tipo' => ['required', Rule::in(Categoria::TIPOS)],
            'estado' => ['required', Rule::in(Categoria::ESTADOS)],
            'orden' => ['required', 'integer', 'min:0', 'max:9999'],
        ];
    }
}
