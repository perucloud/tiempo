<?php

namespace App\Http\Controllers\App\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function show(): View
    {
        return view('app.auth.registro');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombres'               => ['required', 'string', 'max:150'],
            'telefono'              => ['required', 'string', 'max:30', 'unique:clientes,telefono'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'terminos'              => ['accepted'],
        ], [
            'telefono.unique'  => 'Ya existe una cuenta con ese número de celular.',
            'password.min'     => 'La contraseña debe tener al menos 8 caracteres.',
            'terminos.accepted' => 'Debes aceptar los términos y condiciones.',
        ]);

        $cliente = Cliente::create([
            'nombres'  => $data['nombres'],
            'telefono' => $data['telefono'],
            'password' => $data['password'],
            'estado'   => Cliente::ESTADO_ACTIVO,
            'ultimo_acceso'    => now(),
            'ip_ultimo_acceso' => $request->ip(),
        ]);

        Auth::guard('cliente')->login($cliente);
        $request->session()->regenerate();

        return redirect()->route('app.inicio')->with('bienvenida', '¡Bienvenido a TIEMPO, ' . $cliente->nombres . '!');
    }
}
