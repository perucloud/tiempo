<?php

namespace Tests\Unit\Geo;

use App\DTOs\Geo\GeocodeResult;
use App\Services\Geo\NominatimProvider;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NominatimProviderTest extends TestCase
{
    private function reverseFixture(): array
    {
        return [
            'display_name' => 'Jr. Libertad 200, Satipo, Junín, Perú',
            'lat'          => '-11.25',
            'lon'          => '-74.63',
            'address'      => [
                'road'          => 'Jr. Libertad',
                'house_number'  => '200',
                'city_district' => 'Satipo',
                'county'        => 'Provincia de Satipo',
                'state'         => 'Junín',
                'country'       => 'Perú',
            ],
        ];
    }

    public function test_reverse_geocode_returns_geocode_result_on_success(): void
    {
        Http::fake(['*/reverse*' => Http::response($this->reverseFixture(), 200)]);

        $result = (new NominatimProvider())->reverseGeocode(-11.25, -74.63);

        $this->assertInstanceOf(GeocodeResult::class, $result);
        $this->assertSame('Junín', $result->departamento);
    }

    public function test_reverse_geocode_returns_null_on_http_failure(): void
    {
        Http::fake(['*/reverse*' => Http::response([], 500)]);

        $result = (new NominatimProvider())->reverseGeocode(-11.25, -74.63);

        $this->assertNull($result);
    }

    public function test_reverse_geocode_returns_null_when_address_absent(): void
    {
        Http::fake(['*/reverse*' => Http::response(['lat' => '-11.25', 'lon' => '-74.63'], 200)]);

        $result = (new NominatimProvider())->reverseGeocode(-11.25, -74.63);

        $this->assertNull($result);
    }

    public function test_reverse_geocode_returns_null_on_connection_exception(): void
    {
        Http::fake(['*/reverse*' => function () {
            throw new ConnectionException('timed out');
        }]);

        $result = (new NominatimProvider())->reverseGeocode(-11.25, -74.63);

        $this->assertNull($result);
    }

    public function test_search_returns_array_on_success(): void
    {
        $fixture = [['place_id' => 1, 'display_name' => 'Satipo', 'lat' => '-11.25', 'lon' => '-74.63']];
        Http::fake(['*/search*' => Http::response($fixture, 200)]);

        $results = (new NominatimProvider())->search('Satipo');

        $this->assertCount(1, $results);
        $this->assertSame('Satipo', $results[0]['display_name']);
    }

    public function test_search_returns_empty_array_on_http_failure(): void
    {
        Http::fake(['*/search*' => Http::response([], 503)]);

        $this->assertSame([], (new NominatimProvider())->search('Satipo'));
    }

    public function test_search_returns_empty_array_on_connection_exception(): void
    {
        Http::fake(['*/search*' => function () {
            throw new ConnectionException('timeout');
        }]);

        $this->assertSame([], (new NominatimProvider())->search('Satipo'));
    }

    public function test_provider_sends_configured_user_agent(): void
    {
        Http::fake(['*/reverse*' => Http::response($this->reverseFixture(), 200)]);
        config(['geo.geocoding.user_agent' => 'TestApp/2.0']);

        (new NominatimProvider())->reverseGeocode(-11.25, -74.63);

        Http::assertSent(fn ($r) => $r->header('User-Agent')[0] === 'TestApp/2.0');
    }

    public function test_provider_uses_base_url_from_config(): void
    {
        config(['geo.geocoding.base_url' => 'https://custom-nominatim.example.com']);
        Http::fake(['https://custom-nominatim.example.com/*' => Http::response($this->reverseFixture(), 200)]);

        $result = (new NominatimProvider())->reverseGeocode(-11.25, -74.63);

        $this->assertNotNull($result);
        Http::assertSentCount(1);
    }
}
