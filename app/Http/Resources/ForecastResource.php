<?php

namespace App\Http\Resources;

use App\Enums\WeatherCodes;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ForecastResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'temperature' => rtrim($this->temperature, '.0'),
            'wind_speed' => rtrim($this->wind_speed, '.0'),
            'humidity' => rtrim($this->humidity, '.0'),
            'pressure' => rtrim($this->pressure, '.0'),
            'weather_code' => WeatherCodes::fromCode($this->code),
            'weather_name' => WeatherCodes::fromCode($this->code)->name,
            'weather_description' => $this->weather_descriptions,
            'timestamp' => $this->timestamp,
            'location' => new LocationResource($this->whenLoaded('location'))
        ];
    }
}
