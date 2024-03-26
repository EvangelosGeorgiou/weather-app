<?php

namespace App\Exceptions;

class MissingCityAndCoordinates extends \Exception
{
    public function render(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'message' => 'City or coordinates must be provided'
        ], 400);
    }
}
