<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminModuleAccess
{
    /**
     * Verifica que el usuario tenga acceso al módulo indicado.
     * Uso: middleware('admin.module:pedidos')
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasModuleAccess($module)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Acceso denegado.'], 403);
            }

            return redirect()
                ->route('admin.dashboard')
                ->with('status_error', "No tienes acceso al módulo «{$module}».");
        }

        return $next($request);
    }
}
