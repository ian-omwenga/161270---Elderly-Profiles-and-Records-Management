<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ElderProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\AlertController;


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.submit');

Route::get('/register', [AuthController::class, 'showRegister'])
    ->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register.submit');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Elder Profiles
    |--------------------------------------------------------------------------
    */

    // Create elder profile
    Route::get('/elder/create', [ElderProfileController::class, 'create'])
        ->name('elder.create');

    // Store elder profile
    Route::post('/elder', [ElderProfileController::class, 'store'])
        ->name('elder.store');

    // Edit elder profile
    Route::get('/elder/{elder}/edit', [ElderProfileController::class, 'edit'])
        ->name('elder.profile.edit');

    // Update elder profile
    Route::put('/elder/{elder}', [ElderProfileController::class, 'update'])
        ->name('elder.profile.update');


    /*
    |--------------------------------------------------------------------------
    | Caregiver Visits
    |--------------------------------------------------------------------------
    */

    // View visits for an elder
    Route::get('/elder/{elder}/visits', [VisitController::class, 'index'])
        ->name('visits.index');


    /*
    |--------------------------------------------------------------------------
    | Alerts
    |--------------------------------------------------------------------------
    */

    // Mark an alert as read
    Route::patch('/alerts/{alert}/read', [AlertController::class, 'markAsRead'])
        ->name('alerts.read');

});