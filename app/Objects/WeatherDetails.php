<?php

namespace App\Objects;

use App\Enums\WeatherCodes;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

class WeatherDetails implements Arrayable
{
    public int $locationId;
    public Carbon $timestamp;
    public string $temperature;
    public string $humidity;
    public string $windSpeed;
    public string $pressure;
    public WeatherCodes $weatherCode;
    public string $weatherDescription;
    public string $source;

    public function __construct(int $locationId,string $source, Carbon $timestamp, string $temperature, string $humidity, string $windSpeed,
                                string $pressure, WeatherCodes $weatherCode, string $weatherDescription)
    {
        $this->timestamp = $timestamp;
        $this->temperature = $temperature;
        $this->humidity = $humidity;
        $this->windSpeed = $windSpeed;
        $this->pressure = $pressure;
        $this->weatherCode = $weatherCode;
        $this->weatherDescription = $weatherDescription;
        $this->locationId = $locationId;
        $this->source = $source;
    }

    public function toArray(): array
    {
        return [
            'location_id' => $this->locationId,
            'timestamp' => $this->timestamp->toDateTimeString(),
            'source' => $this->source,
            'temperature' => $this->temperature,
            'humidity' => $this->humidity,
            'wind_speed' => $this->windSpeed,
            'pressure' => $this->pressure,
            'weather_code' => $this->weatherCode->value,
            'weather_description' => $this->weatherDescription
        ];
    }
}
