<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Services\CustomerOtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class CustomerAccessController extends Controller
{
    public function requestCode(Request $request, CustomerOtpService $otp): JsonResponse
    {
        $data = $request->validate(['telefono' => ['required', 'regex:/^[0-9]{9,15}$/']]);

        try {
            $code = $otp->issue($data['telefono']);
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 503);
        }

        $response = ['message' => 'Si el número tiene pedidos, enviamos un código de verificación.'];

        if (app()->environment(['local', 'testing'])) {
            $response['debug_code'] = $code;
        }

        return response()->json($response);
    }

    public function verifyCode(Request $request, CustomerOtpService $otp): JsonResponse
    {
        $data = $request->validate([
            'telefono' => ['required', 'regex:/^[0-9]{9,15}$/'],
            'codigo' => ['required', 'digits:6'],
        ]);

        $cliente = Cliente::query()->where('telefono', $data['telefono'])->first();

        if (! $cliente || ! $otp->verify($data['telefono'], $data['codigo'])) {
            return response()->json(['message' => 'El código es inválido o expiró.'], 422);
        }

        $request->session()->regenerate();
        $request->session()->put('app_customer_phone', $cliente->telefono);
        $request->session()->put('app_order_ids', $cliente->pedidos()
            ->latest()->limit(100)->pluck('id')->map(fn ($id) => (int) $id)->all());

        return response()->json([
            'message' => 'Teléfono verificado correctamente.',
            'telefono' => $cliente->telefono,
        ]);
    }
}
