<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Security\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ConsumerProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CooperativeProfileController;
use App\Http\Controllers\ProducerProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ProducerLocationController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

Route::resource('producers/map', ProducerLocationController::class)->only('index')->names('producer.map');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('users', UserController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::get('photo/serve/{photo}', [FileController::class, 'servePhoto'])->name('photo.serve')->middleware('signed');

    // profiles
    Route::singleton('producers/profile', ProducerProfileController::class)->only(['show', 'update'])->names('profile.producer');
    Route::singleton('cooperatives/profile', CooperativeProfileController::class)->only(['show', 'update'])->names('profile.cooperative');
    Route::singleton('consumers/profile', ConsumerProfileController::class)->only(['show', 'update'])->names('profile.consumer');

});

require __DIR__ . '/auth.php';
