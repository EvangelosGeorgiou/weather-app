<?php

namespace App\Integrations;

use App\Enums\WeatherCodes;
use App\Models\Location;
use App\Objects\LocationDetails;
use App\Objects\WeatherDetails;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;

class WeatherBit extends WeatherApi
{

    public function url(): string
    {
        return 'https://api.weatherbit.io/v2.0/forecast/hourly';
    }

    public function getApiKey(): string
    {
        return env('WEATHER_BIT_API_KEY');
    }


    /**
     * @throws Exception
     */
    public function getForecastWeather(Location $location): ?Collection
    {
        $response = $this->call($this->url(), [
            'lat' => $location->latitude,
            'lon' => $location->longitude,
            'key' => env('WEATHER_BIT_API_KEY')
        ]);

        return collect($response->json('data'))
            ->map(function ($weatherData) use ($location) {
                return new WeatherDetails(
                    locationId: $location->id,
                    source: 'weatherbit',
                    timestamp: Carbon::make($weatherData['timestamp_local']),
                    temperature: $weatherData['temp'],
                    humidity: $weatherData['rh'],
                    windSpeed: $weatherData['wind_spd'],
                    pressure: $weatherData['pres'],
                    weatherCode: WeatherCodes::fromCode($weatherData['weather']['code']),
                    weatherDescription: $weatherData['weather']['description'],
                );
            });
    }
}
