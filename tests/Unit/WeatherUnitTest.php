<?php

namespace Tests\Unit;

use App\Enums\WeatherCodes;
use App\Models\Forecast;
use App\Models\Location;
use App\Objects\WeatherDetails;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class WeatherUnitTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_that_if_i_give_wrong_data_to_save_forecasts_it_will_be_skipped(): void
    {
        /** @var Location $location */
        $location = Location::factory()->create();
        $data = new Collection([
            [
                'something' => 1,
            ],
            [
                'something' => 2
            ],
            new WeatherDetails(
                $location->id,
                'weatherbit',
                now(),
                20,
                50,
                10,
                1000,
                WeatherCodes::ATMOSPHERE,
                'Clear sky'
            )
        ]);

        $location->saveForecasts($data);

        $this->assertDatabaseCount(Forecast::class, 1);
    }
}
