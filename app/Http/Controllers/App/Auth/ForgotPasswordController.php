<?php

namespace App\Http\Controllers\App\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Services\CustomerOtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use RuntimeException;

class ForgotPasswordController extends Controller
{
    public function show(): View
    {
        return view('app.auth.recuperar');
    }

    public function sendCode(Request $request, CustomerOtpService $otp): JsonResponse
    {
        $data = $request->validate(['telefono' => ['required', 'regex:/^[0-9]{9,15}$/']]);

        try {
            $code = $otp->issue($data['telefono']);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        }

        $response = ['message' => 'Si el número tiene una cuenta, enviamos un código de verificación.'];

        if (app()->environment(['local', 'testing'])) {
            $response['debug_code'] = $code;
        }

        return response()->json($response);
    }

    public function verify(Request $request, CustomerOtpService $otp): JsonResponse
    {
        $data = $request->validate([
            'telefono' => ['required', 'regex:/^[0-9]{9,15}$/'],
            'codigo'   => ['required', 'digits:6'],
        ]);

        if (! $otp->verify($data['telefono'], $data['codigo'])) {
            return response()->json(['message' => 'El código es inválido o expiró.'], 422);
        }

        // Guardar token temporal en sesión para permitir el reset
        $request->session()->put('otp_verified_phone', $data['telefono']);

        return response()->json(['message' => 'Código verificado. Ahora puedes cambiar tu contraseña.']);
    }

    public function reset(Request $request): JsonResponse|RedirectResponse
    {
        $telefono = $request->session()->get('otp_verified_phone');

        if (! $telefono) {
            return response()->json(['message' => 'Sesión expirada. Vuelve a solicitar el código.'], 422);
        }

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $cliente = Cliente::query()->where('telefono', $telefono)->first();

        if (! $cliente) {
            return response()->json(['message' => 'No se encontró la cuenta.'], 422);
        }

        $cliente->update(['password' => $data['password']]);
        $request->session()->forget('otp_verified_phone');

        Auth::guard('cliente')->login($cliente);

        return response()->json([
            'message'  => 'Contraseña actualizada. Redirigiendo…',
            'redirect' => route('app.inicio'),
        ]);
    }
}
