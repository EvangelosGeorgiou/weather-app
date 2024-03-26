<?php

namespace App\Objects;

use App\Models\Location;
use Illuminate\Contracts\Support\Arrayable;

class LocationDetails implements Arrayable
{

    public string $country;
    public string $code;
    public string $city;
    public string $latitude;
    public string $longitude;

    public function __construct(string $country, string $code, string $city, string $latitude, string $longitude)
    {
        $this->country = $country;
        $this->code = $code;
        $this->city = $city;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public static function constructFromModel(Location $model): static
    {
        return new static($model->country, $model->code, $model->city, $model->latitude, $model->longitude);
    }

    public function toArray(): array
    {
        return [
            'country' => $this->country,
            'code' => $this->code,
            'city' => $this->city,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude
        ];
    }
}
