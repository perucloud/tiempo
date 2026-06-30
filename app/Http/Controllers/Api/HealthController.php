<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return ApiResponse::success(
            data: [
                'service' => 'TIEMPO Delivery API',
                'status' => 'ok',
                'version' => 'v1',
            ],
            message: 'API disponible.',
            meta: [
                'consumers' => [
                    'cliente',
                    'repartidor',
                    'negocio_afiliado',
                    'admin_operador',
                ],
            ],
        );
    }
}
