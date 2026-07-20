<?php

namespace App\Http\Requests\Admin;

use App\Models\NegocioAfiliado;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBusinessRequest extends FormRequest
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
            'nombre_comercial' => [
                'required',
                'string',
                'max:255',
                Rule::unique('negocios_afiliados', 'nombre_comercial')
                    ->ignore($this->route('business'))
                    ->whereNull('deleted_at'),
            ],
            'tipo_negocio' => ['required', Rule::in(NegocioAfiliado::TIPOS)],
            'ruc' => ['nullable', 'string', 'max:20'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'estado' => ['required', Rule::in(NegocioAfiliado::ESTADOS)],
            'abierto' => ['required', 'boolean'],
            'horarios_texto' => ['nullable', 'string', 'max:500'],
            'imagen'              => ['nullable', 'string', 'max:500'],
            'slogan'              => ['nullable', 'string', 'max:120'],
            'precio_minimo'       => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'color_marca'         => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'hora_apertura'       => ['nullable', 'date_format:H:i'],
            'hora_cierre'         => ['nullable', 'date_format:H:i'],
            'tiempo_preparacion'  => ['nullable', 'integer', 'min:0', 'max:240'],
            'departamento'        => ['nullable', 'string', 'max:80'],
            'provincia'           => ['nullable', 'string', 'max:80'],
            'distrito'            => ['nullable', 'string', 'max:80'],
            'referencia'          => ['nullable', 'string', 'max:255'],
            'latitud'             => ['nullable', 'numeric', 'between:-90,90'],
            'longitud'            => ['nullable', 'numeric', 'between:-180,180'],
            'celular'             => ['nullable', 'string', 'max:20'],
            'whatsapp'            => ['nullable', 'string', 'max:20'],
            'telefono_fijo'       => ['nullable', 'string', 'max:20'],
            'pagina_web'          => ['nullable', 'string', 'max:255'],
            'facebook'            => ['nullable', 'string', 'max:255'],
            'instagram'           => ['nullable', 'string', 'max:255'],
            'tiktok'              => ['nullable', 'string', 'max:255'],
        ];
    }
}
