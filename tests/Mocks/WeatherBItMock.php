<?php

namespace Tests\Mocks;

use App\Contacts\WeatherApi;
use App\Enums\WeatherCodes;
use App\Integrations\WeatherBit;
use App\Models\Location;
use App\Objects\WeatherDetails;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;

class WeatherBItMock extends WeatherBit
{
    use WithFaker;

    /**
     * @throws Exception
     */
    public function getForecastWeather(Location $location): ?Collection
    {
        for ($i = 0; $i < 5; $i++) {
            $data[] = new WeatherDetails(
                locationId: $location->id,
                source: 'weatherbit',
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

    public function getApiKey(): string
    {
        return 'mock-key';
    }

    public function url(): string
    {
        return 'mock-url';
    }
}
