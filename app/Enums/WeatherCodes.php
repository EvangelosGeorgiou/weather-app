<?php

namespace App\Enums;

use Exception;
use function PHPUnit\Framework\matches;

enum WeatherCodes: int
{
    case THUNDERSTORM = 200;
    case DRIZZLE = 300;
    case RAIN = 500;
    case SNOW = 600;
    case ATMOSPHERE = 700;
    case CLEAR = 800;
    case CLOUDS = 801;

    /**
     * @throws Exception
     */
    public static function fromCode($code): WeatherCodes
    {
        switch ($code){
            case $code >= 200 && $code < 300:
                return self::THUNDERSTORM;
            case $code >= 300 && $code < 400:
                return self::DRIZZLE;
            case $code >= 500 && $code < 600:
                return self::RAIN;
            case $code >= 600 && $code < 700:
                return self::SNOW;
            case $code >= 700 && $code < 800:
                return self::ATMOSPHERE;
            case $code === 800:
                return self::CLEAR;
            case $code > 800:
                return self::CLOUDS;
        }

        throw new Exception('Invalid weather code');
    }

}
