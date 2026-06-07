<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Citizen\DashboardController;
use App\Http\Controllers\Citizen\ReportController;
use App\Http\Controllers\Citizen\VehicleController;
use App\Http\Controllers\Citizen\ViolationController;
use Illuminate\Support\Facades\RateLimiter;
use App\Http\Controllers\Citizen\ProfileController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

RateLimiter::for('reports', function (\Illuminate\Http\Request $request) {
    return \Illuminate\Cache\RateLimiting\Limit::perMinute(3)->by($request->ip());
});

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
], function () {

    Route::get('/', function () {
        return view('welcome');
    })->name('home');

    Route::middleware('guest')->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login']);
        Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('register', [RegisterController::class, 'register']);
    });

    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    Route::middleware('auth')->prefix('citizen')->name('citizen.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
        Route::get('/vehicles/{vehicle}', [VehicleController::class, 'show'])->name('vehicles.show');
        Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
        Route::put('/vehicles/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update');
        Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
        Route::post('/reports', [ReportController::class, 'store'])
            ->middleware('throttle:reports')
            ->name('reports.store');
        Route::get('/reports/search-vehicles', [ReportController::class, 'searchVehicles'])->name('reports.search-vehicles');
        Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
        Route::get('/violations', [ViolationController::class, 'index'])->name('violations.index');
        Route::get('/violations/{violation}', [ViolationController::class, 'show'])->name('violations.show');
        Route::post('/violations/{violation}/pay', [ViolationController::class, 'mockPay'])->name('violations.pay');
        
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile/info', [ProfileController::class, 'updateInfo'])->name('profile.update-info');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    });

});
