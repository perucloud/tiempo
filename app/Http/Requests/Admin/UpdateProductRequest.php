<?php

namespace App\Http\Requests\Admin;

use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
            'negocio_afiliado_id' => ['required', 'exists:negocios_afiliados,id'],
            'categoria_id' => ['nullable', 'exists:categorias,id'],
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'precio' => ['required', 'numeric', 'min:0.1', 'max:99999.99'],
            'precio_promocional' => ['nullable', 'numeric', 'min:0.1', 'max:99999.99', 'lt:precio'],
            'imagen' => ['nullable', 'url', 'max:255'],
            'estado' => ['required', Rule::in(Producto::ESTADOS)],
            'disponible' => ['required', 'boolean'],
        ];
    }
}
