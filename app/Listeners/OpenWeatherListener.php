<?php

namespace App\Listeners;

use App\Events\WeatherDataCollectionEvent;
use App\Integrations\OpenWeather;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;


class OpenWeatherListener implements ShouldQueue
{
    private OpenWeather $openWeather;

    /**
     * Create the event listener.
     */
    public function __construct(OpenWeather $openWeather)
    {
        $this->openWeather = $openWeather;
    }

    /**
     * Handle the event.
     * @throws Exception
     */
    public function handle(WeatherDataCollectionEvent $event): void
    {
        $event->location->saveForecasts($this->openWeather->getForecastWeather($event->location));
    }
}
