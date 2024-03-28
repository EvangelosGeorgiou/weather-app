<?php

namespace Tests\Feature;

use App\Contacts\GatherLocationDetails;
use App\Enums\WeatherCodes;
use App\Events\WeatherDataCollectionEvent;
use App\Integrations\OpenWeather;
use App\Integrations\WeatherBit;
use App\Jobs\WeatherDataCollectionJob;
use App\Models\Forecast;
use App\Models\Location;
use App\Objects\LocationDetails;
use App\Objects\WeatherDetails;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\TestCase;

class WeatherApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_that_i_can_get_the_correct_location_details()
    {
        Event::fake();

        if ($this->app->environment('ci')) {
            $location = Location::factory([
                'city' => 'Limassol',
                'code' => 'CY',
            ])->create();
            $locationDetails = new LocationDetails($location->country, $location->code, $location->city, $location->latitude, $location->longitude);

            $this->mock(GatherLocationDetails::class, function (MockInterface $mock) use ($locationDetails) {
                $mock->shouldReceive('getLocationBasedOnCity')->andReturn($locationDetails);
                $mock->shouldReceive('getLocationBasedOnCoordinates')->andReturn($locationDetails);
            });
        }

        $response = $this->post('api/city', ['city' => 'Limassol']);
        $location = $response->json('data');

        $this->assertSame('Limassol', $location['city']);
        $this->assertSame('CY', $location['code']);

        $coordinatesResponse = $this->post('api/coordinates', ['lat' => $location['latitude'], 'lon' => $location['longitude']]);
        $coordinatesLocation = $coordinatesResponse->json('data');
        $this->assertSame('Limassol', $coordinatesLocation['city']);
        $this->assertSame('CY', $coordinatesLocation['code']);

        Event::assertDispatched(WeatherDataCollectionEvent::class);
    }

    public function test_register_locations_with_wrong_data()
    {
        $response = $this->post('api/city');
        $response->assertStatus(422);
        $this->assertSame('City is required', $response->json('city.0'));

        $response = $this->post('api/coordinates');
        $response->assertStatus(422);
        $this->assertSame('Latitude is required', $response->json('lat.0'));
        $this->assertSame('Longitude is required', $response->json('lon.0'));

    }

    public function test_that_the_forecast_data_are_saved()
    {
        $location = Location::query()->create([
            'country' => 'Cyprus',
            'code' => 'CY',
            'city' => 'Nicosia',
            'latitude' => 35.1748976,
            'longitude' => 33.3638568,
        ]);

        $this->mock(OpenWeather::class, function (MockInterface $mock) use ($location) {
            $mock->shouldReceive('getForecastWeather')->andReturn($this->getMockData($location->id, 'openweathermap', 5));
        });

        $this->mock(WeatherBit::class, function (MockInterface $mock) use ($location) {
            $mock->shouldReceive('getForecastWeather')->andReturn($this->getMockData($location->id, 'weatherbit', 5));
        });

        (new WeatherDataCollectionJob())();

        $forecasts = Forecast::all();
        $this->assertCount(10, $forecasts);
        $this->assertCount(5, $forecasts->where('source', 'openweathermap'));
        $this->assertCount(5, $forecasts->where('source', 'weatherbit'));
    }

    private function getMockData(int $locationId, string $weatherSource, int $count = 1): Collection
    {
        for ($i = 0; $i < $count; $i++) {
            $data[] = new WeatherDetails(
                locationId: $locationId,
                source: $weatherSource,
                timestamp: Carbon::now()->subMinutes($i * 10),
                temperature: rand(10, 50),
                humidity: rand(0, 100),
                windSpeed: rand(0, 100),
                pressure: rand(0, 100),
                weatherCode: WeatherCodes::ATMOSPHERE,
                weatherDescription: 'mock-description'
            );
        }

        return collect($data);
    }
}
