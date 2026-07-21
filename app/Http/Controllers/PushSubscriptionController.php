<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function customer(Request $request): JsonResponse
    {
        $phone = (string) $request->session()->get('app_customer_phone', '');
        $customer = Cliente::query()->where('telefono', $phone)->firstOrFail();
        return $this->store($request, ['cliente_id' => $customer->id, 'repartidor_id' => null]);
    }

    public function courier(Request $request): JsonResponse
    {
        $courier = $request->user()?->repartidor;
        abort_unless($courier, 403);
        return $this->store($request, ['cliente_id' => null, 'repartidor_id' => $courier->id]);
    }

    private function store(Request $request, array $owner): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => ['required', 'url', 'max:2000'],
            'keys.p256dh' => ['required', 'string', 'max:500'],
            'keys.auth' => ['required', 'string', 'max:500'],
            'contentEncoding' => ['nullable', 'string', 'max:30'],
        ]);

        PushSubscription::query()->updateOrCreate(
            ['endpoint_hash' => hash('sha256', $data['endpoint'])],
            $owner + [
                'endpoint' => $data['endpoint'], 'public_key' => $data['keys']['p256dh'],
                'auth_token' => $data['keys']['auth'], 'content_encoding' => $data['contentEncoding'] ?? 'aes128gcm',
                'user_agent' => substr((string) $request->userAgent(), 0, 500), 'last_used_at' => now(),
            ],
        );

        return response()->json(['message' => 'Notificaciones activadas.']);
    }
}
