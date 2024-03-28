<?php

namespace App\Jobs;

use App\Events\WeatherDataCollectionEvent;
use App\Integrations\OpenWeather;
use App\Integrations\WeatherBit;
use App\Models\Location;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WeatherDataCollectionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private OpenWeather $openWeather;
    private WeatherBit $weatherStack;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     */
    public function __invoke(): void
    {
        logger()->channel('info')->info('Weather data collection job started');

        Location::all()
            ->map(function (Location $location) {
                event(new WeatherDataCollectionEvent($location));
            });

        logger()->channel('info')->info('Weather data collection job finished');
    }

}
