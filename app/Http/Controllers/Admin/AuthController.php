<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        [$a, $b] = $this->generateCaptcha();
        return view('admin.auth.login', compact('a', 'b'));
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Validar captcha matemático (omitido en entorno de testing)
        if (! app()->environment('testing')) {
            $given    = (int) $request->input('captcha_sum', -1);
            $expected = (int) session('captcha_answer', -99);

            if ($given !== $expected) {
                [$a, $b] = $this->generateCaptcha();
                return back()
                    ->withErrors(['captcha_sum' => 'Respuesta incorrecta. Intenta de nuevo.'])
                    ->onlyInput('email')
                    ->with(compact('a', 'b'));
            }
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Las credenciales no son válidas.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        if (! $request->user()?->canAccessAdmin()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['email' => 'Este usuario no tiene acceso al panel administrativo.'])
                ->onlyInput('email');
        }

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    // ── Recuperar contraseña ──────────────────────────────────────────────

    public function showRecover(): View
    {
        return view('admin.auth.recuperar');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'Te enviamos el enlace de recuperación. Revisa tu correo.')
            : back()->withErrors(['email' => 'No encontramos una cuenta con ese correo.']);
    }

    public function showReset(string $token): View
    {
        return view('admin.auth.reset', ['token' => $token]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token'                 => ['required'],
            'email'                 => ['required', 'email'],
            'password'              => ['required', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password): void {
                $user->forceFill(['password' => $password])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('admin.login')->with('status', 'Contraseña actualizada correctamente. Ya puedes ingresar.')
            : back()->withErrors(['email' => __($status)]);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function generateCaptcha(): array
    {
        $a = random_int(1, 15);
        $b = random_int(1, 15);
        session(['captcha_answer' => $a + $b]);
        return [$a, $b];
    }
}
