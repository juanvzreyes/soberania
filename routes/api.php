<?php

use App\Http\Controllers\LocationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/locations/states', [LocationController::class, 'getStates'])->name('api.locations.states');
Route::get('/locations/states/{state}/municipalities', [LocationController::class, 'getMunicipalities'])->name('api.locations.municipalities');
Route::get('/locations/municipalities/{municipality}/neighborhoods', [LocationController::class, 'getNeighborhoods'])->name('api.locations.neighborhoods');
Route::get('/locations/postal-code/{postalCode}', [LocationController::class, 'getPostalCode'])->name('api.locations.postal-code');
