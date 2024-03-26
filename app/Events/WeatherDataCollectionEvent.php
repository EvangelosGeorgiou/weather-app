<?php

namespace App\Events;

use App\Models\Location;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WeatherDataCollectionEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Location $location;

    /**
     * Create a new event instance.
     */
    public function __construct(Location $location)
    {
        $this->location = $location;
    }
}
