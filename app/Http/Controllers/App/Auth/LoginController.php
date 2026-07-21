<?php

namespace App\Http\Controllers\App\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showWelcome(): View|RedirectResponse
    {
        if (Auth::guard('cliente')->check()) {
            return redirect()->route('app.inicio');
        }

        return view('app.auth.bienvenida');
    }

    public function showLogin(): View
    {
        return view('app.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'telefono' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::guard('cliente')->attempt(['telefono' => $credentials['telefono'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();

            /** @var \App\Models\Cliente $cliente */
            $cliente = Auth::guard('cliente')->user();
            $cliente->update([
                'ultimo_acceso'    => now(),
                'ip_ultimo_acceso' => $request->ip(),
            ]);

            return redirect()->intended(route('app.inicio'));
        }

        return back()->withErrors(['telefono' => 'El teléfono o contraseña no son válidos.'])->onlyInput('telefono');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('cliente')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('app.home');
    }
}
