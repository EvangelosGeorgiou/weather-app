<?php

namespace App\Exceptions;

class CityProvidedNotFound extends \Exception
{
    public function render()
    {
        return response()->json([
            'message' => 'City provided not found'
        ], 404);
    }
}
