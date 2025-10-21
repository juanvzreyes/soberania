<?php

namespace App\Http\Controllers;

use App\Models\Producer;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class ProducerLocationController extends Controller
{
    private string $source;

    public function __construct()
    {
        $this->source = 'ProducerMap/Pages/';
    }

    public function index()
    {
        $producers = Producer::with(['location', 'user'])
            ->whereHas('location', function ($query) {
                $query->whereNotNull('latitude')
                    ->whereNotNull('longitude');
            })->get();

        $locations = $producers->map(function ($producer) {
            return [
                'id' => $producer->id,
                'certification' => $producer->certification,
                'name' => $producer->user->name,
                'latitude' => $producer->location->latitude,
                'longitude' => $producer->location->longitude,
            ];
        });

        return Inertia::render("{$this->source}Index", [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'locations' => $locations,
        ]);
    }
}
