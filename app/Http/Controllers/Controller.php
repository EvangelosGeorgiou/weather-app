<?php

namespace App\Http\Controllers;

use App\Contacts\GatherLocationDetails;
use App\Events\WeatherDataCollectionEvent;
use App\Exceptions\CityProvidedNotFound;
use App\Exceptions\CoordinatesProvidedNotFound;
use App\Exceptions\LocationDetailsNotFound;
use App\Exceptions\MissingCityAndCoordinates;
use App\Http\Requests\CityRegisterRequest;
use App\Http\Requests\CoordinatesRegisterRequest;
use App\Http\Resources\ForecastResource;
use App\Http\Resources\LocationResource;
use App\Models\Forecast;
use App\Models\Location;
use App\Objects\LocationDetails;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Throwable;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    private GatherLocationDetails $gathersLocationDetails;

    public function __construct(GatherLocationDetails $gathersLocationDetails)
    {
        $this->gathersLocationDetails = $gathersLocationDetails;
    }

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     * @throws Exception
     * @throws Throwable
     *
     * @api /api/forecast?lat=123&lon=123
     * @api /api/forecast?city=Limassol
     * @api /api/forecast?city=Limassol&date=2024-03-26
     * @api /api/forecast?lat=123&lon=123&from_date=2024-03-26&to_date<=2024-03-27
     */
    public function getForecast(Request $request): AnonymousResourceCollection
    {
        $cacheKey = 'forecast-' . implode('-', $request->input());

        $locationDetails = null;
        if ($request->has('lat') && $request->has('lon')) {
            $locationDetails = $this->gathersLocationDetails->getLocationBasedOnCoordinates($request->input('lat'), $request->input('lon'));
        } else if ($request->has('city')) {
            $locationDetails = $this->gathersLocationDetails->getLocationBasedOnCity($request->input('city'));
        }

        throw_if(is_null($locationDetails), new LocationDetailsNotFound());

        $location = Location::query()
            ->where([
                'city' => $locationDetails->city,
                'code' => $locationDetails->code,
            ])->firstOrFail();

        if (Cache::has($cacheKey)) {
            return ForecastResource::collection(Cache::get($cacheKey));
        }

        $data = Forecast::getAggregatedDataBasedOnLocation($location->id)
            ->when($request->has('date'), function ($query) use ($request) {
                $query->whereDate('timestamp', Carbon::parse($request->input('date'))->toDate());
            })->when($request->has('from_date') && $request->has('to_date'), function ($query) use ($request) {
                $query->whereDate('timestamp', '>=', Carbon::parse($request->input('from_date'))->toDate())
                    ->whereDate('timestamp', '<=', Carbon::parse($request->input('to_date'))->toDate());
            })
            ->with('location')
            ->paginate($request->input('page-size', 10), ['*'], 'page', $request->input('page', 1));

        Cache::remember($cacheKey, -1, function () use ($data) {
            return $data;
        });

        return ForecastResource::collection($data);
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function registerCity(CityRegisterRequest $request): LocationResource
    {
        $data = $request->validated();

        $locationDetails = $this->gathersLocationDetails->getLocationBasedOnCity($data['city']);

        throw_if(is_null($locationDetails), new CityProvidedNotFound());

        /** @var Location $location */
        $location = Location::query()
            ->firstOrCreate(
                ['code' => $locationDetails->code, 'city' => $locationDetails->city],
                $locationDetails->toArray()
            );

        event(new WeatherDataCollectionEvent($location));

        return new LocationResource($location);
    }

    /**
     * @throws Throwable
     */
    public function registerCoordinates(CoordinatesRegisterRequest $request): LocationResource
    {
        $data = $request->validated();

        $locationDetails = $this->gathersLocationDetails->getLocationBasedOnCoordinates($data['lat'], $data['lon']);

        throw_if(is_null($locationDetails), new CoordinatesProvidedNotFound);

        /** @var Location $location */
        $location = Location::query()
            ->firstOrCreate(
                ['code' => $locationDetails->code, 'city' => $locationDetails->city],
                $locationDetails->toArray()
            );

        event(new WeatherDataCollectionEvent($location));

        return new LocationResource($location);
    }

    public function locations(Request $request): AnonymousResourceCollection
    {
        $locations = Location::query()
            ->when($request->has('city'), function ($query) use ($request) {
                $query->where('city', $request->input('city'));
            })
            ->when($request->has('code'), function ($query) use ($request) {
                $query->where('code', $request->input('code'));
            })
            ->paginate($request->input('page-size', 10), ['*'], 'page', $request->input('page', 1));

        return LocationResource::collection($locations);
    }
}
