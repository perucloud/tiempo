<?php

namespace App\Contracts\Geo;

use App\DTOs\Geo\RouteResult;

interface RoutingProviderInterface
{
    /**
     * Calcula la ruta vial entre dos coordenadas GPS.
     * Devuelve RouteResult con routeFound=false si no existe ruta viable.
     *
     * @throws \App\Exceptions\Geo\RoutingException en error de red o respuesta inválida
     */
    public function route(
        float $originLat,
        float $originLng,
        float $destLat,
        float $destLng,
    ): RouteResult;
}
