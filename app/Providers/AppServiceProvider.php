<?php

namespace App\Providers;

use App\Contacts\GatherLocationDetails;
use App\Integrations\OpenWeather;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            GatherLocationDetails::class,
            OpenWeather::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
