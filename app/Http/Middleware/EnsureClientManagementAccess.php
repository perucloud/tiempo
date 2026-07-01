<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClientManagementAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, [
            User::ROLE_SUPERADMIN,
            User::ROLE_ADMIN,
            User::ROLE_OPERADOR,
        ], true)) {
            abort(403);
        }

        return $next($request);
    }
}
