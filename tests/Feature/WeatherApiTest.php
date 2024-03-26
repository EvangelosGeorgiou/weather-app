<?php

namespace Tests\Feature;

use App\Events\WeatherDataCollectionEvent;
use App\Integrations\OpenWeather;
use App\Integrations\WeatherBit;
use App\Jobs\WeatherDataCollectionJob;
use App\Models\Forecast;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\Mocks\WeatherBItMock;
use Tests\Mocks\OpenWeatherMock;
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

        $response = $this->post('api/city', ['city' => 'Limassol']);
        $response->assertStatus(201);
        $location = $response->json('data');

        $this->assertSame('Limassol', $location['city']);
        $this->assertSame('CY', $location['code']);

        $coordinatesResponse = $this->post('api/coordinates', ['lat' => $location['latitude'], 'lon' => $location['longitude']]);
        $coordinatesResponse->assertStatus(200);
        $coordinatesLocation = $coordinatesResponse->json('data');
        $this->assertSame('Limassol', $coordinatesLocation['city']);
        $this->assertSame('CY', $coordinatesLocation['code']);

        Event::assertDispatched(WeatherDataCollectionEvent::class);
    }

    public function test_register_locations_with_wrong_data()
    {
        $response = $this->post('api/city');
        $response->assertStatus(422);
        $this->assertSame('City is required',$response->json('city.0'));

        $response = $this->post('api/coordinates');
        $response->assertStatus(422);
        $this->assertSame('Latitude is required',$response->json('lat.0'));
        $this->assertSame('Longitude is required',$response->json('lon.0'));

    }

    public function test_that_the_forecast_data_are_saved()
    {
        Location::query()->create([
            'country' => 'Cyprus',
            'code' => 'CY',
            'city' => 'Nicosia',
            'latitude' => 35.1748976,
            'longitude' => 33.3638568,
        ]);

        $this->app->bind(OpenWeather::class, OpenWeatherMock::class);
        $this->app->bind(WeatherBit::class, WeatherBItMock::class);

        (new WeatherDataCollectionJob())->handle();

        $forecasts = Forecast::all();
        $this->assertCount(10, $forecasts);
        $this->assertCount(5, $forecasts->where('source', 'openweathermap'));
        $this->assertCount(5, $forecasts->where('source', 'weatherbit'));
    }
}
