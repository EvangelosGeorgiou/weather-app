<?php

namespace App\Exceptions;

class LocationDetailsNotFound extends \Exception
{
    public function render(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'message' => 'Location details not found'
        ], 400);
    }
}
