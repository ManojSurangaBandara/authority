<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\BusRouteController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\EscortController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {

    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Add other protected routes here
    Route::resource('buses', BusController::class);
    Route::resource('bus-routes', BusRouteController::class);
    Route::resource('drivers', DriverController::class);
    Route::get('drivers-api/get-details', [DriverController::class, 'getDriverDetails'])->name('drivers.get-details');
    
    // Escort routes
    Route::resource('escorts', EscortController::class);
    Route::get('escorts-api/get-details', [EscortController::class, 'getEscortDetails'])->name('escorts.get-details');
});

Route::get('/logout', function () {
    Auth::logout();
    return redirect('/login');
});

// Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
