<?php

namespace App\Contacts;

use App\Objects\LocationDetails;

interface GatherLocationDetails
{
    public function getLocationBasedOnCity(string $city): ?LocationDetails;

    public function getLocationBasedOnCoordinates(float $lat, float $lon): ?LocationDetails;
}
