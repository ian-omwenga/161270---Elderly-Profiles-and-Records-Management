<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ElderProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\AlertController;

/* Authentication */

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.submit');

Route::get('/register', [AuthController::class, 'showRegister'])
    ->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register.submit');

/* Only logged-in users can access these routes */

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Log out
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    // Elder profiles
    Route::get('/elder/create', [ElderProfileController::class, 'create'])
        ->name('elder.create');

    Route::post('/elder', [ElderProfileController::class, 'store'])
        ->name('elder.store');

    Route::get('/elder/{elder}/edit', [ElderProfileController::class, 'edit'])
        ->name('elder.profile.edit');

    Route::put('/elder/{elder}', [ElderProfileController::class, 'update'])
        ->name('elder.profile.update');

    // View an elder's visit history
    Route::get('/elder/{elder}/visits', [VisitController::class, 'index'])
        ->name('visits.index');

    // Open the caregiver visit-entry form
    Route::get('/elder/{elder}/visits/create', [VisitController::class, 'create'])
        ->name('visits.create');

    // Save a submitted visit
    Route::post('/elder/{elder}/visits', [VisitController::class, 'store'])
        ->name('visits.store');

    // Mark an alert as read
    Route::patch('/alerts/{alert}/read', [AlertController::class, 'markAsRead'])
        ->name('alerts.read');
});
