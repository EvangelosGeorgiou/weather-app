<?php

namespace App\Contacts;

use App\Models\Location;
use App\Objects\WeatherDetails;
use Illuminate\Support\Collection;

interface WeatherApi
{
    /**
     * @param Location $location
     * @return Collection<WeatherDetails>|null
     */
    public function getForecastWeather(Location $location): ?Collection;

    public function getApiKey(): string;

    public function url(): string;
}
