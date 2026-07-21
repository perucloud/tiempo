<?php

namespace App\Services\Geo;

/**
 * Genera el payload de configuración que se inyecta como window.GeoConfig
 * en las vistas Blade que necesitan llamadas client-side a proveedores de mapas.
 */
class MapConfigurationService
{
    /**
     * @return array<string, mixed>
     */
    public function jsConfig(): array
    {
        return [
            'geocodingBase' => config('geo.geocoding.base_url'),
            'mapProvider'   => config('geo.map_provider'),
            'countryCode'   => config('geo.defaults.country_code', 'pe'),
            'language'      => config('geo.defaults.language', 'es'),
            'resultLimit'   => (int) config('geo.defaults.result_limit', 5),
        ];
    }
}
