<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ElderProfileController;
use App\Http\Controllers\FamilyDashboardController;
use App\Http\Controllers\DashboardController;


/*
Authentication
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
Family
*/

Route::middleware('auth')->group(function () {

    Route::get('/elder/create',
        [ElderProfileController::class, 'create']
    )->name('elder.create');

    Route::post('/elder',
        [ElderProfileController::class, 'store']
    )->name('elder.store');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

});