<?php

namespace Tests\Unit\Geo;

use App\DTOs\Geo\GeocodeResult;
use PHPUnit\Framework\TestCase;

class GeocodeResultTest extends TestCase
{
    private function nominatimFixture(): array
    {
        return [
            'display_name' => 'Jr. Dos de Mayo, 110, Satipo, Junín, Perú',
            'lat'          => '-11.2534',
            'lon'          => '-74.6362',
            'address'      => [
                'road'          => 'Jr. Dos de Mayo',
                'house_number'  => '110',
                'city_district' => 'Satipo',
                'county'        => 'Provincia de Satipo',
                'state'         => 'Junín',
                'postcode'      => '12851',
                'country'       => 'Perú',
            ],
        ];
    }

    public function test_from_nominatim_response_constructs_correct_dto(): void
    {
        $result = GeocodeResult::fromNominatimResponse($this->nominatimFixture());

        $this->assertSame('Jr. Dos de Mayo', $result->calle);
        $this->assertSame('110', $result->numero);
        $this->assertSame('Jr. Dos de Mayo, 110', $result->direccion);
        $this->assertSame('Satipo', $result->distrito);
        $this->assertSame('Provincia de Satipo', $result->provincia);
        $this->assertSame('Junín', $result->departamento);
        $this->assertSame('12851', $result->codigoPostal);
        $this->assertSame('Perú', $result->pais);
        $this->assertSame(-11.2534, $result->lat);
        $this->assertSame(-74.6362, $result->lng);
    }

    public function test_from_nominatim_response_handles_missing_optional_fields(): void
    {
        $result = GeocodeResult::fromNominatimResponse([
            'lat'     => '-12.0',
            'lon'     => '-77.0',
            'address' => [],
        ]);

        $this->assertSame('', $result->calle);
        $this->assertSame('', $result->numero);
        $this->assertSame('', $result->direccion);
        $this->assertSame('', $result->distrito);
        $this->assertSame(-12.0, $result->lat);
    }

    public function test_from_nominatim_response_falls_back_suburb_when_city_district_absent(): void
    {
        $data = $this->nominatimFixture();
        unset($data['address']['city_district']);
        $data['address']['suburb'] = 'El Carmen';

        $result = GeocodeResult::fromNominatimResponse($data);

        $this->assertSame('El Carmen', $result->distrito);
    }

    public function test_to_array_returns_backward_compatible_keys(): void
    {
        $result = GeocodeResult::fromNominatimResponse($this->nominatimFixture());
        $arr    = $result->toArray();

        $expectedKeys = [
            'display_name', 'direccion', 'calle', 'numero',
            'distrito', 'provincia', 'departamento', 'codigo_postal',
            'pais', 'lat', 'lng',
        ];
        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $arr, "Missing key: {$key}");
        }
    }

    public function test_to_array_preserves_lat_lng_as_float(): void
    {
        $result = GeocodeResult::fromNominatimResponse($this->nominatimFixture());
        $arr    = $result->toArray();

        $this->assertIsFloat($arr['lat']);
        $this->assertIsFloat($arr['lng']);
    }

    public function test_direccion_omits_separator_when_house_number_absent(): void
    {
        $data = $this->nominatimFixture();
        unset($data['address']['house_number']);

        $result = GeocodeResult::fromNominatimResponse($data);

        $this->assertSame('Jr. Dos de Mayo', $result->direccion);
        $this->assertSame('', $result->numero);
    }
}
