<?php

namespace App\Models;

use App\Objects\WeatherDetails;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Location extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function forecasts(): HasMany
    {
        return $this->hasMany(Forecast::class);
    }

    /**
     * @param Collection<WeatherDetails>|null $data
     * @return void
     */
    public function saveForecasts(?Collection $data): void
    {
        if (is_null($data) || $data->isEmpty()) {
            return;
        }

        $data = $data->whereInstanceOf(WeatherDetails::class);

        try {
            DB::transaction(function () use ($data){
                Forecast::query()->upsert(
                    $data->toArray(),
                    ['location_id', 'timestamp', 'source'],
                    ['temperature', 'humidity', 'wind_speed', 'pressure', 'weather_code', 'weather_description']
                );
            });
        }catch (\Exception $e) {
            logger()->channel('error')->error('Failed to save forecasts', ['error' => $e->getMessage()]);
        }
    }
}
