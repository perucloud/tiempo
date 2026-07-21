<?php

namespace Tests\Unit\Geo;

use App\Services\Geo\MapConfigurationService;
use Tests\TestCase;

class MapConfigurationServiceTest extends TestCase
{
    public function test_js_config_returns_array_with_all_required_keys(): void
    {
        $config = (new MapConfigurationService())->jsConfig();

        foreach (['geocodingBase', 'mapProvider', 'countryCode', 'language', 'resultLimit'] as $key) {
            $this->assertArrayHasKey($key, $config, "Missing key: {$key}");
        }
    }

    public function test_geocoding_base_matches_config(): void
    {
        config(['geo.geocoding.base_url' => 'https://nominatim.example.com']);

        $config = (new MapConfigurationService())->jsConfig();

        $this->assertSame('https://nominatim.example.com', $config['geocodingBase']);
    }

    public function test_result_limit_is_integer(): void
    {
        $config = (new MapConfigurationService())->jsConfig();

        $this->assertIsInt($config['resultLimit']);
    }

    public function test_country_code_defaults_to_pe(): void
    {
        $config = (new MapConfigurationService())->jsConfig();

        $this->assertSame('pe', $config['countryCode']);
    }
}
