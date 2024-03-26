<?php

namespace App\Listeners;

use App\Events\WeatherDataCollectionEvent;
use App\Integrations\WeatherBit;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;

class WeatherBitListener implements ShouldQueue
{
    private WeatherBit $weatherBit;

    /**
     * Create the event listener.
     */
    public function __construct(WeatherBit $weatherBit)
    {
        $this->weatherBit = $weatherBit;
    }

    /**
     * Handle the event.
     * @throws Exception
     */
    public function handle(WeatherDataCollectionEvent $event): void
    {
        $event->location->saveForecasts($this->weatherBit->getForecastWeather($event->location));
    }
}
