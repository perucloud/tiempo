<?php

namespace App\Http\Requests\Admin;

use App\Models\Pago;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewPaymentRequest extends FormRequest
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
            'estado' => ['required', Rule::in([Pago::ESTADO_APROBADO, Pago::ESTADO_RECHAZADO])],
            'observacion' => ['nullable', 'string', 'max:500'],
        ];
    }
}
