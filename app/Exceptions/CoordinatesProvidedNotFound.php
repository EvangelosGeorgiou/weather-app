<?php

namespace App\Exceptions;

class CoordinatesProvidedNotFound extends CityProvidedNotFound
{
    public function render()
    {
        return response()->json([
            'message' => 'Coordinates provided not found'
        ], 404);
    }
}
