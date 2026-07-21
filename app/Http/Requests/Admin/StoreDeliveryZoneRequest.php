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
            'nombre'                => ['required', 'string', 'max:120'],
            'descripcion_cobertura' => ['nullable', 'string', 'max:500'],

            /* Polígono: JSON string de [[lng, lat], ...] con al menos 3 puntos,
               o vacío/null si aún no se ha dibujado */
            'polygon_json'          => ['nullable', 'string'],

            /* Tarificación */
            'costo_delivery'        => ['required', 'numeric', 'min:0', 'max:9999'],
            'km_incluidos'          => ['nullable', 'numeric', 'min:0', 'max:999'],
            'precio_por_km_extra'   => ['nullable', 'numeric', 'min:0', 'max:999'],
            'delivery_gratis_desde' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'recargo'               => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'distancia_maxima_km'   => ['nullable', 'numeric', 'min:0.1', 'max:999'],
            'pedido_minimo'         => ['nullable', 'numeric', 'min:0', 'max:9999'],

            /* Tiempos estimados */
            'tiempo_estimado_min'   => ['nullable', 'integer', 'min:1', 'max:300'],
            'tiempo_estimado_max'   => ['nullable', 'integer', 'min:1', 'max:300'],

            /* Prioridad y estado */
            'prioridad'             => ['nullable', 'integer', 'min:1', 'max:99'],
            'activo'                => ['nullable', 'boolean'],
        ];
    }
}
