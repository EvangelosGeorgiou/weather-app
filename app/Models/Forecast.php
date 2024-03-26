<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Forecast extends Model
{
    use HasFactory;

    protected $guarded = ['id'];


    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class)
            ->select(['id', 'country', 'code', 'city', 'latitude', 'longitude']);
    }

    public static function getAggregatedDataBasedOnLocation(int $locationId): Builder
    {
        return self::query()
            ->selectRaw("
                 location_id,
                 timestamp,
                 AVG(temperature) as temperature,
                 AVG(wind_speed) as wind_speed,
                 AVG(humidity) as humidity,
                 AVG(pressure) as pressure,
                 AVG(weather_code) as code,
                 GROUP_CONCAT(DISTINCT forecasts.weather_description) as weather_descriptions
                 ")
            ->groupBy('timestamp', 'location_id')
            ->orderBy('timestamp')
            ->where('location_id', $locationId);
    }

}
