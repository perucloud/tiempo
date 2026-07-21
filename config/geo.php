<?php

return [

    'map_provider'       => env('MAP_PROVIDER', 'openstreetmap'),
    'geocoding_provider' => env('GEOCODING_PROVIDER', 'nominatim'),
    'routing_provider'   => env('ROUTING_PROVIDER', 'osrm'),

    'geocoding' => [
        'base_url'   => env('GEOCODING_BASE_URL', 'https://nominatim.openstreetmap.org'),
        'timeout'    => (int) env('GEOCODING_TIMEOUT', 5),
        'user_agent' => env('GEOCODING_USER_AGENT', 'TiempoDelivery/1.0'),
    ],

    'routing' => [
        'base_url' => env('ROUTING_BASE_URL', 'https://router.project-osrm.org'),
        'timeout'  => (int) env('ROUTING_TIMEOUT', 8),
        'profile'  => env('ROUTING_PROFILE', 'driving'),
    ],

    'defaults' => [
        'country_code' => 'pe',
        'language'     => 'es',
        'result_limit' => 5,
    ],

];
