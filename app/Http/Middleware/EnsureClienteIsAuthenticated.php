<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClienteIsAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->guard('cliente')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No autenticado.'], 401);
            }

            return redirect()->route('app.login')->with('redirect_after_login', $request->url());
        }

        return $next($request);
    }
}
