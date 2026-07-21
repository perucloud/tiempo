<?php

namespace App\Http\Middleware;

use App\Models\Repartidor;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCourierAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $courier = $request->route('repartidor');
        if (! $courier instanceof Repartidor && is_numeric($courier)) {
            $courier = Repartidor::query()->find((int) $courier);
        }
        $user = $request->user();

        if (! $user || $user->status !== User::STATUS_ACTIVE || $user->role !== User::ROLE_REPARTIDOR
            || ! $courier instanceof Repartidor || $courier->user_id !== $user->id) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
