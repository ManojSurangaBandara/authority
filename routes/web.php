<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\BusAssignmentController;
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
use App\Http\Controllers\BusPassIntegrationController;
use App\Http\Controllers\QrDownloadController;
use App\Http\Controllers\EstablishmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LivingInBusController;
use App\Http\Controllers\DestinationLocationController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\GsDivisionController;
use App\Http\Controllers\PoliceStationController;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {

    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // API routes for dropdowns (accessible by all authenticated users)
    Route::get('destination-locations-api/all', [App\Http\Controllers\DestinationLocationController::class, 'api'])->name('destination-locations.api');
    Route::get('bus-routes-api/all', [App\Http\Controllers\BusRouteController::class, 'api'])->name('bus-routes.api');
    Route::get('living-in-buses-api/all', [App\Http\Controllers\LivingInBusController::class, 'api'])->name('living-in-buses.api');
    Route::get('route-statistics-api/{application}/{routeName}/{routeType}', [App\Http\Controllers\BusPassApprovalController::class, 'getRouteStatistics'])->name('route-statistics.api');

    // Add other protected routes here
    Route::resource('buses', BusController::class);
    Route::resource('bus-routes', BusRouteController::class);

    // Bus Assignment routes
    Route::get('bus-assignments', [BusAssignmentController::class, 'index'])->name('bus-assignments.index');
    Route::post('bus-assignments/assign', [BusAssignmentController::class, 'assign'])->name('bus-assignments.assign');
    Route::post('bus-assignments/unassign', [BusAssignmentController::class, 'unassign'])->name('bus-assignments.unassign');
    Route::get('bus-assignments/data', [BusAssignmentController::class, 'getAssignmentData'])->name('bus-assignments.data');

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
    Route::get('bus-pass-applications/create-civil', [BusPassApplicationController::class, 'createCivil'])->name('bus-pass-applications.create-civil');
    Route::get('bus-pass-applications/create-navy', [BusPassApplicationController::class, 'createNavy'])->name('bus-pass-applications.create-navy');
    Route::get('bus-pass-applications/create-airforce', [BusPassApplicationController::class, 'createAirforce'])->name('bus-pass-applications.create-airforce');
    Route::resource('bus-pass-applications', BusPassApplicationController::class);
    Route::get('bus-pass-applications-api/get-details', [BusPassApplicationController::class, 'getPersonDetails'])->name('bus-pass-applications.get-details');
    Route::post('bus-pass-applications-api/verify-branch-card', [BusPassApplicationController::class, 'verifyBranchCard'])->name('bus-pass-applications.verify-branch-card');

    // QR Download routes
    Route::get('qr-download', [QrDownloadController::class, 'index'])->name('qr-download.index');
    Route::get('qr-download/{id}/download', [QrDownloadController::class, 'download'])->name('qr-download.download');

    // Bus Pass Status routes (Master Data)
    Route::resource('bus-pass-statuses', BusPassStatusController::class);

    // Bus Pass Approval routes
    Route::get('bus-pass-approvals', [BusPassApprovalController::class, 'index'])->name('bus-pass-approvals.index');
    Route::post('bus-pass-approvals/{application}/approve', [BusPassApprovalController::class, 'approve'])->name('bus-pass-approvals.approve');
    Route::post('bus-pass-approvals/{application}/reject', [BusPassApprovalController::class, 'reject'])->name('bus-pass-approvals.reject');
    Route::post('bus-pass-approvals/{application}/recommend', [BusPassApprovalController::class, 'recommend'])->name('bus-pass-approvals.recommend');
    Route::post('bus-pass-approvals/{application}/not-recommend', [BusPassApprovalController::class, 'notRecommend'])->name('bus-pass-approvals.not-recommend');
    Route::post('bus-pass-approvals/{application}/dmov-not-recommend', [BusPassApprovalController::class, 'dmovNotRecommend'])->name('bus-pass-approvals.dmov-not-recommend');
    Route::post('bus-pass-approvals/{application}/forward-to-branch-clerk', [BusPassApprovalController::class, 'forwardToBranchClerk'])->name('bus-pass-approvals.forward-to-branch-clerk');
    Route::get('bus-pass-approvals/{application}/modal', [BusPassApprovalController::class, 'loadModal'])->name('bus-pass-approvals.modal');
    Route::post('bus-pass-approvals/{application}/update-route', [BusPassApprovalController::class, 'updateRoute'])->name('bus-pass-approvals.update-route');

    // Bus Pass Integration routes (DMOV access only)
    Route::get('bus-pass-integration', [BusPassIntegrationController::class, 'index'])->name('bus-pass-integration.index');
    Route::get('bus-pass-integration/chart-data', [BusPassIntegrationController::class, 'getChartData'])->name('bus-pass-integration.chart-data');
    Route::get('bus-pass-integration/applications', [BusPassIntegrationController::class, 'getApplications'])->name('bus-pass-integration.applications');
    Route::get('bus-pass-integration/{id}', [BusPassIntegrationController::class, 'show'])->name('bus-pass-integration.show');
    Route::post('bus-pass-integration/{id}/integrate', [BusPassIntegrationController::class, 'integrate'])->name('bus-pass-integration.integrate');
    Route::post('bus-pass-integration/{id}/undo-integrate', [BusPassIntegrationController::class, 'undoIntegrate'])->name('bus-pass-integration.undo-integrate');

    // Bus Driver Assignment routes
    Route::resource('bus-driver-assignments', BusDriverAssignmentController::class);
    Route::post('bus-driver-assignments/assign', [BusDriverAssignmentController::class, 'assign'])->name('bus-driver-assignments.assign');
    Route::post('bus-driver-assignments/unassign', [BusDriverAssignmentController::class, 'unassign'])->name('bus-driver-assignments.unassign');
    Route::get('bus-driver-assignments/data', [BusDriverAssignmentController::class, 'getAssignmentData'])->name('bus-driver-assignments.data');
    Route::get('bus-driver-assignments-api/get-driver-details', [BusDriverAssignmentController::class, 'getDriverDetails'])->name('bus-driver-assignments.get-driver-details');
    Route::get('bus-driver-assignments-api/get-bus-details', [BusDriverAssignmentController::class, 'getBusDetails'])->name('bus-driver-assignments.get-bus-details');

    // Bus Escort Assignment routes
    Route::get('bus-escort-assignments', [BusEscortAssignmentController::class, 'index'])->name('bus-escort-assignments.index');
    Route::post('bus-escort-assignments/assign', [BusEscortAssignmentController::class, 'assign'])->name('bus-escort-assignments.assign');
    Route::post('bus-escort-assignments/unassign', [BusEscortAssignmentController::class, 'unassign'])->name('bus-escort-assignments.unassign');
    Route::get('bus-escort-assignments/data', [BusEscortAssignmentController::class, 'getAssignmentData'])->name('bus-escort-assignments.data');

    // SLCMP In-charge Assignment routes
    Route::resource('slcmp-incharge-assignments', SlcmpInchargeAssignmentController::class);
    Route::post('slcmp-incharge-assignments/assign', [SlcmpInchargeAssignmentController::class, 'assign'])->name('slcmp-incharge-assignments.assign');
    Route::post('slcmp-incharge-assignments/unassign', [SlcmpInchargeAssignmentController::class, 'unassign'])->name('slcmp-incharge-assignments.unassign');
    Route::get('slcmp-incharge-assignments/data', [SlcmpInchargeAssignmentController::class, 'getAssignmentData'])->name('slcmp-incharge-assignments.data');
    Route::get('slcmp-incharge-assignments-api/get-slcmp-details', [SlcmpInchargeAssignmentController::class, 'getSlcmpDetails'])->name('slcmp-incharge-assignments.get-slcmp-details');
    Route::get('slcmp-incharge-assignments-api/get-bus-details', [SlcmpInchargeAssignmentController::class, 'getBusDetails'])->name('slcmp-incharge-assignments.get-bus-details');

    // Bus Filling Station Assignment routes
    Route::resource('bus-filling-station-assignments', BusFillingStationAssignmentController::class);
    Route::post('bus-filling-station-assignments/assign', [BusFillingStationAssignmentController::class, 'assign'])->name('bus-filling-station-assignments.assign');
    Route::post('bus-filling-station-assignments/unassign', [BusFillingStationAssignmentController::class, 'unassign'])->name('bus-filling-station-assignments.unassign');
    Route::get('bus-filling-station-assignments/data', [BusFillingStationAssignmentController::class, 'getAssignmentData'])->name('bus-filling-station-assignments.data');
    Route::get('bus-filling-station-assignments-api/get-bus-details', [BusFillingStationAssignmentController::class, 'getBusDetails'])->name('bus-filling-station-assignments.get-bus-details');

    // Establishment routes
    Route::get('establishment/seniority-order', [EstablishmentController::class, 'seniorityOrder'])->name('establishment.seniority-order');
    Route::post('establishment/update-seniority-order', [EstablishmentController::class, 'updateSeniorityOrder'])->name('establishment.update-seniority-order');
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

        // Living In Buses routes
        Route::resource('living-in-buses', LivingInBusController::class);

        // Destination Locations routes
        Route::resource('destination-locations', DestinationLocationController::class);

        // Province routes
        Route::resource('province', ProvinceController::class);

        // District routes
        Route::resource('district', DistrictController::class);

        // GS Division routes
        Route::resource('gs-devision', GsDivisionController::class);

        // Police Station routes
        Route::resource('police-station', PoliceStationController::class);
    });

    Route::get('rejected-applications', [ReportController::class, 'rejected'])->name('rejected-applications.index');

    Route::get('temporary-card-printed', [ReportController::class, 'temporary_card_printed'])->name('temporary-card-printed.index');

    Route::get('handed-over-applications', [ReportController::class, 'handed_over'])->name('handed-over-applications.index');

    Route::get('not-yet-handed-over-applications', [ReportController::class, 'not_yet_handed_over'])->name('not-yet-handed-over-applications.index');

    Route::get('integrated-applications', [ReportController::class, 'integrated'])->name('integrated-applications.index');

    Route::get('pending-applications', [ReportController::class, 'pending'])->name('pending-applications.index');


    Route::get('integrated-to-build-card', [ReportController::class, 'build'])->name('integrated-to-build-card.index');

    Route::get('passenger-counts', [ReportController::class, 'passengerCounts'])->name('passenger-counts.index');

    Route::get('living-out-passenger-counts', [ReportController::class, 'livingOutPassengerCounts'])->name('living-out-passenger-counts.index');

    Route::get('living-in-passenger-counts', [ReportController::class, 'livingInPassengerCounts'])->name('living-in-passenger-counts.index');

    Route::get('establishment-wise-applications', [ReportController::class, 'establishmentWiseApplications'])->name('establishment-wise-applications.index');

    Route::get('route-establishment-report', [ReportController::class, 'routeEstablishmentReport'])->name('route-establishment-report.index');

    Route::get('onboarded-passengers', [ReportController::class, 'onboarded_passengers'])->name('onboarded-passengers.index');

    // Profile routes (All authenticated users)
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');

    // Change Password routes (System Administrator only)
    Route::middleware('role:System Administrator (DMOV)')->group(function () {
        Route::get('profile/change-password', [ProfileController::class, 'editPassword'])->name('profile.change-password');
        Route::post('profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    });
});

Route::get('/logout', function () {
    Auth::logout();
    return redirect('/login');
});

// Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
