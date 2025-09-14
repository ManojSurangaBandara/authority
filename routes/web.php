<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\BusDriverAssignmentController;
use App\Http\Controllers\BusEscortAssignmentController;
use App\Http\Controllers\BusRouteController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\EscortController;
use App\Http\Controllers\SlcmpInchargeController;
use App\Http\Controllers\FillingStationController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\BusPassApplicationController;
use App\Http\Controllers\BusPassStatusController;
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

    // SLCMP In Charge routes
    Route::resource('slcmp-incharges', SlcmpInchargeController::class);
    Route::get('slcmp-incharges-api/get-details', [SlcmpInchargeController::class, 'getSlcmpInchargeDetails'])->name('slcmp-incharges.get-details');

    // Filling Station routes
    Route::resource('filling-stations', FillingStationController::class);

    // Person routes
    Route::resource('persons', PersonController::class);
    Route::get('persons-api/get-details', [PersonController::class, 'getPersonDetails'])->name('persons.get-details');

    // Bus Pass Application routes
    Route::resource('bus-pass-applications', BusPassApplicationController::class);
    Route::get('bus-pass-applications-api/get-details', [BusPassApplicationController::class, 'getPersonDetails'])->name('bus-pass-applications.get-details');

    // Bus Pass Status routes (Master Data)
    Route::resource('bus-pass-statuses', BusPassStatusController::class);

    // Bus Driver Assignment routes
    Route::resource('bus-driver-assignments', BusDriverAssignmentController::class);
    Route::get('bus-driver-assignments-api/get-driver-details', [BusDriverAssignmentController::class, 'getDriverDetails'])->name('bus-driver-assignments.get-driver-details');
    Route::get('bus-driver-assignments-api/get-bus-details', [BusDriverAssignmentController::class, 'getBusDetails'])->name('bus-driver-assignments.get-bus-details');

    // Bus Escort Assignment routes
    Route::resource('bus-escort-assignments', BusEscortAssignmentController::class);
    Route::get('bus-escort-assignments-api/get-escort-details', [BusEscortAssignmentController::class, 'getEscortDetails'])->name('bus-escort-assignments.get-escort-details');
    Route::get('bus-escort-assignments-api/get-bus-details', [BusEscortAssignmentController::class, 'getBusDetails'])->name('bus-escort-assignments.get-bus-details');
});

Route::get('/logout', function () {
    Auth::logout();
    return redirect('/login');
});

// Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
