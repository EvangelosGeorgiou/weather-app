<?php

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('forecast', [Controller::class, 'getForecast']);
Route::get('locations', [Controller::class, 'locations']);
Route::post('city', [Controller::class, 'registerCity']);
Route::post('coordinates', [Controller::class, 'registerCoordinates']);
