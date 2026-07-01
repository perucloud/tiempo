<?php

namespace App\Http\Requests\Admin;

use App\Models\Repartidor;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignCourierRequest extends FormRequest
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
            'repartidor_id' => [
                'required',
                Rule::exists('repartidores', 'id')->whereNull('deleted_at'),
            ],
            'comentario' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $courier = Repartidor::query()->find($this->input('repartidor_id'));
            $order = $this->route('order');

            if ($courier && $courier->estado === Repartidor::ESTADO_INACTIVO) {
                $validator->errors()->add('repartidor_id', 'El repartidor seleccionado esta inactivo.');
            }

            if (
                $courier
                && $courier->estado === Repartidor::ESTADO_OCUPADO
                && (int) $order?->repartidor_id !== (int) $courier->id
            ) {
                $validator->errors()->add('repartidor_id', 'El repartidor seleccionado ya esta ocupado.');
            }
        });
    }
}
