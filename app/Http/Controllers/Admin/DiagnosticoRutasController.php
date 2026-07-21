<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Geo\RoutingProviderInterface;
use App\Exceptions\Geo\RoutingException;
use App\Services\Geo\MapConfigurationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DiagnosticoRutasController extends Controller
{
    public function show(MapConfigurationService $mapConfig): \Illuminate\View\View
    {
        return view('admin.diagnostico.rutas', [
            'geoConfig' => $mapConfig->jsConfig(),
        ]);
    }

    public function calcular(Request $request, RoutingProviderInterface $routing): JsonResponse
    {
        $validated = $request->validate([
            'origin_latitude'       => ['required', 'numeric', 'between:-90,90'],
            'origin_longitude'      => ['required', 'numeric', 'between:-180,180'],
            'destination_latitude'  => ['required', 'numeric', 'between:-90,90'],
            'destination_longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        try {
            $result = $routing->route(
                (float) $validated['origin_latitude'],
                (float) $validated['origin_longitude'],
                (float) $validated['destination_latitude'],
                (float) $validated['destination_longitude'],
            );

            return response()->json($result->toArray());

        } catch (RoutingException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
