<?php

namespace App\Integrations;

use App\Contacts\GatherLocationDetails;
use App\Enums\WeatherCodes;
use App\Models\Location;
use App\Objects\LocationDetails;
use App\Objects\WeatherDetails;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class OpenWeather extends WeatherApi implements GatherLocationDetails
{
    /**
     * @throws Exception
     */
    public function getForecastWeather(Location $location): ?Collection
    {
        $response = $this->call($this->url() . 'data/2.5/forecast', [
            'lat' => $location->latitude,
            'lon' => $location->longitude,
            'units' => 'metric', // celsius
            'appid' => env('OPEN_WEATHER_API_KEY')
        ]);

        return collect($response->json('list'))
            ->map(function ($weatherData) use ($location) {
                return new WeatherDetails(
                    locationId: $location->id,
                    source: 'openweathermap',
                    timestamp: Carbon::createFromTimestamp($weatherData['dt']),
                    temperature: $weatherData['main']['temp'],
                    humidity: $weatherData['main']['humidity'],
                    windSpeed: $weatherData['wind']['speed'],
                    pressure: $weatherData['main']['pressure'],
                    weatherCode: WeatherCodes::fromCode($weatherData['weather'][0]['id']),
                    weatherDescription: $weatherData['weather'][0]['description'],
                );
            });
    }

    public function getApiKey(): string
    {
        return env('OPEN_WEATHER_API_KEY');
    }

    public function url(): string
    {
        return 'api.openweathermap.org/';
    }

    /**
     * @throws Exception
     */
    public function getLocationBasedOnCity(string $city): ?LocationDetails
    {
        return Cache::rememberForever("city-location-{$city}", function () use ($city) {
            $response = $this->call($this->url() . 'geo/1.0/direct', [
                'q' => $city,
                'limit' => 1,
                'appid' => $this->getApiKey()
            ]);

            $data = $response->collect()->first();

            if (empty($data)) {
                return null;
            }

            return new LocationDetails(
                $data['state'] ?? $data['country'],
                $data['country'],
                $data['local_names']['feature_name'] ?? $data['local_names']['en'],
                $data['lat'],
                $data['lon']);
        });
    }

    /**
     * @throws Exception
     */
    public function getLocationBasedOnCoordinates(float $lat, float $lon): ?LocationDetails
    {
        return Cache::rememberForever("coordinates-location-{$lat}-{$lon}", function () use ($lat, $lon) {
            $response = $this->call($this->url() . 'geo/1.0/reverse', [
                'lat' => $lat,
                'lon' => $lon,
                'limit' => 1,
                'appid' => env('OPEN_WEATHER_API_KEY')
            ]);

            $data = $response->collect()->first();

            if (empty($data)) {
                return null;
            }

            return new LocationDetails(
                country: $data['state'] ?? $data['country'],
                code: $data['country'],
                city: $data['local_names']['feature_name'] ?? $data['local_names']['en'],
                latitude: $data['lat'],
                longitude: $data['lon']
            );
        });
    }
}
