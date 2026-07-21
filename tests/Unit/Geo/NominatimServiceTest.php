<?php

namespace Tests\Unit\Geo;

use App\Contracts\Geo\GeocodingProviderInterface;
use App\DTOs\Geo\GeocodeResult;
use App\Services\NominatimService;
use Mockery;
use Tests\TestCase;

class NominatimServiceTest extends TestCase
{
    private function makeResult(): GeocodeResult
    {
        return new GeocodeResult(
            displayName:  'Jr. Libertad 200, Satipo',
            direccion:    'Jr. Libertad, 200',
            calle:        'Jr. Libertad',
            numero:       '200',
            distrito:     'Satipo',
            provincia:    'Provincia de Satipo',
            departamento: 'Junín',
            codigoPostal: '12851',
            pais:         'Perú',
            lat:          -11.25,
            lng:          -74.63,
        );
    }

    public function test_reverse_geocode_delegates_to_provider_and_returns_array(): void
    {
        $provider = Mockery::mock(GeocodingProviderInterface::class);
        $provider->shouldReceive('reverseGeocode')
            ->once()
            ->with(-11.25, -74.63)
            ->andReturn($this->makeResult());

        $result = (new NominatimService($provider))->reverseGeocode(-11.25, -74.63);

        $this->assertIsArray($result);
        $this->assertSame('Junín', $result['departamento']);
        $this->assertArrayHasKey('display_name', $result);
    }

    public function test_reverse_geocode_returns_null_when_provider_returns_null(): void
    {
        $provider = Mockery::mock(GeocodingProviderInterface::class);
        $provider->shouldReceive('reverseGeocode')->andReturn(null);

        $result = (new NominatimService($provider))->reverseGeocode(-11.25, -74.63);

        $this->assertNull($result);
    }

    public function test_search_delegates_to_provider_and_returns_raw_array(): void
    {
        $expected = [['place_id' => 99, 'display_name' => 'Satipo']];
        $provider = Mockery::mock(GeocodingProviderInterface::class);
        $provider->shouldReceive('search')->once()->with('Satipo', 'pe')->andReturn($expected);

        $result = (new NominatimService($provider))->search('Satipo');

        $this->assertSame($expected, $result);
    }

    public function test_search_passes_custom_country_code_to_provider(): void
    {
        $provider = Mockery::mock(GeocodingProviderInterface::class);
        $provider->shouldReceive('search')->once()->with('Lima', 'cl')->andReturn([]);

        (new NominatimService($provider))->search('Lima', 'cl');
    }
}
