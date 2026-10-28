
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ElderProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\SentimentAnalysisController;
use App\Http\Controllers\CaregiverDashboardController;
use App\Http\Controllers\AdminDashboardController;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

/*
|--------------------------------------------------------------------------
| Authenticated Application Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Family Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Caregiver Dashboard
    Route::get('/caregiver/dashboard', [CaregiverDashboardController::class, 'index'])->name('caregiver.dashboard');

    // Admin Dashboard
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::patch('/admin/users/{user}/role', [AdminDashboardController::class, 'updateUserRole'])->name('admin.users.role');

    // Dedicated Elder Profiles
    Route::get('/elder/create', [ElderProfileController::class, 'create'])->name('elder.create');
    Route::post('/elder', [ElderProfileController::class, 'store'])->name('elder.store');
    Route::get('/elder/{elder}', [ElderProfileController::class, 'show'])->name('elder.show');
    Route::get('/elder/{elder}/photo', [ElderProfileController::class, 'photo'])->name('elder.photo');
    Route::get('/elder/{elder}/edit', [ElderProfileController::class, 'edit'])->name('elder.profile.edit');
    Route::put('/elder/{elder}', [ElderProfileController::class, 'update'])->name('elder.profile.update');

    // Caregiver Visits & Procedures Logging
    Route::get('/elder/{elder}/visits', [VisitController::class, 'index'])->name('visits.index');
    Route::get('/elder/{elder}/visits/create', [VisitController::class, 'create'])->name('visits.create');
    Route::post('/elder/{elder}/visits', [VisitController::class, 'store'])->name('visits.store');

    // Alerts
    Route::patch('/alerts/{alert}/read', [AlertController::class, 'markAsRead'])->name('alerts.read');

    // Sentiment Analysis
    Route::get('/sentiment-analysis', [SentimentAnalysisController::class, 'index'])->name('sentiment.index');
    Route::get('/sentiment-analysis/export', [SentimentAnalysisController::class, 'export'])->name('sentiment.export');
});
