<?php

namespace Tests;

use Database\Seeders\WeatherDataSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();


//        $this->seed([
//            WeatherDataSeeder::class
//        ]);
    }
}
