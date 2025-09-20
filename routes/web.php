<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\BusDriverAssignmentController;
use App\Http\Controllers\BusEscortAssignmentController;
use App\Http\Controllers\SlcmpInchargeAssignmentController;
use App\Http\Controllers\BusFillingStationAssignmentController;
use App\Http\Controllers\BusRouteController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\EscortController;
use App\Http\Controllers\SlcmpInchargeController;
use App\Http\Controllers\FillingStationController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\BusPassApplicationController;
use App\Http\Controllers\BusPassStatusController;
use App\Http\Controllers\BusPassApprovalController;
use App\Http\Controllers\EstablishmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ReportController;

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

    // Bus Pass Approval routes
    Route::get('bus-pass-approvals', [BusPassApprovalController::class, 'index'])->name('bus-pass-approvals.index');
    Route::post('bus-pass-approvals/{application}/approve', [BusPassApprovalController::class, 'approve'])->name('bus-pass-approvals.approve');
    Route::post('bus-pass-approvals/{application}/reject', [BusPassApprovalController::class, 'reject'])->name('bus-pass-approvals.reject');

    // Bus Driver Assignment routes
    Route::resource('bus-driver-assignments', BusDriverAssignmentController::class);
    Route::get('bus-driver-assignments-api/get-driver-details', [BusDriverAssignmentController::class, 'getDriverDetails'])->name('bus-driver-assignments.get-driver-details');
    Route::get('bus-driver-assignments-api/get-bus-details', [BusDriverAssignmentController::class, 'getBusDetails'])->name('bus-driver-assignments.get-bus-details');

    // Bus Escort Assignment routes
    Route::resource('bus-escort-assignments', BusEscortAssignmentController::class);
    Route::get('bus-escort-assignments-api/get-escort-details', [BusEscortAssignmentController::class, 'getEscortDetails'])->name('bus-escort-assignments.get-escort-details');
    Route::get('bus-escort-assignments-api/get-bus-details', [BusEscortAssignmentController::class, 'getBusDetails'])->name('bus-escort-assignments.get-bus-details');

    // SLCMP In-charge Assignment routes
    Route::resource('slcmp-incharge-assignments', SlcmpInchargeAssignmentController::class);
    Route::get('slcmp-incharge-assignments-api/get-slcmp-details', [SlcmpInchargeAssignmentController::class, 'getSlcmpDetails'])->name('slcmp-incharge-assignments.get-slcmp-details');
    Route::get('slcmp-incharge-assignments-api/get-bus-details', [SlcmpInchargeAssignmentController::class, 'getBusDetails'])->name('slcmp-incharge-assignments.get-bus-details');

    // Bus Filling Station Assignment routes
    Route::resource('bus-filling-station-assignments', BusFillingStationAssignmentController::class);
    Route::get('bus-filling-station-assignments-api/get-bus-details', [BusFillingStationAssignmentController::class, 'getBusDetails'])->name('bus-filling-station-assignments.get-bus-details');

    // Establishment routes
    Route::resource('establishment', EstablishmentController::class);

    // User Management routes (System Administrator only)
    Route::middleware('role:System Administrator (DMOV)')->group(function () {
        // User routes
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{id}', [UserController::class, 'show'])->name('users.show');
        Route::get('users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::patch('users/{id}', [UserController::class, 'update']);
        Route::delete('users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::patch('users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::patch('users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

        // Role Management routes
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('roles/{id}', [RoleController::class, 'show'])->name('roles.show');
        Route::get('roles/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('roles/{id}', [RoleController::class, 'update'])->name('roles.update');
        Route::patch('roles/{id}', [RoleController::class, 'update']);
        Route::delete('roles/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::get('roles/{id}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
        Route::patch('roles/{id}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.update-permissions');
        Route::get('roles-hierarchy', [RoleController::class, 'hierarchy'])->name('roles.hierarchy');
    });

    Route::get('rejected-applications', [ReportController::class, 'rejected'])->name('rejected-applications.index');

    Route::get('temporary-card-printed', [ReportController::class, 'temporary_card_printed'])->name('temporary-card-printed.index');

});

Route::get('/logout', function () {
    Auth::logout();
    return redirect('/login');
});

// Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
