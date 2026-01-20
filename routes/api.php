<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EscortAuthController;
use App\Http\Controllers\Api\IncidentApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes
Route::get('/', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Authority Management System API',
        'version' => '1.0',
        'endpoints' => [
            'auth' => [
                'login' => 'POST /api/auth/login',
                'register' => 'POST /api/auth/register',
                'logout' => 'POST /api/auth/logout',
                'refresh' => 'POST /api/auth/refresh',
                'me' => 'GET /api/auth/me',
                'change-password' => 'POST /api/auth/change-password',
            ],
            'escort_auth' => [
                'login' => 'POST /api/escort/auth/login',
                'logout' => 'POST /api/escort/auth/logout',
                'refresh' => 'POST /api/escort/auth/refresh',
                'me' => 'GET /api/escort/auth/me',
            ]
        ]
    ]);
});

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

// Escort mobile app authentication (public)
Route::post('/escort/auth/login', [EscortAuthController::class, 'login']);

// Protected routes
Route::middleware('auth:api')->group(function () {
    // Authentication routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/change-password', [AuthController::class, 'changePassword']);


    // API test route
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// Escort protected routes (require escort JWT token)
Route::middleware(['escort.auth'])->prefix('escort')->group(function () {
    Route::post('/auth/logout', [EscortAuthController::class, 'logout']);
    Route::post('/auth/refresh', [EscortAuthController::class, 'refresh']);
    Route::get('/auth/me', [EscortAuthController::class, 'me']);
    Route::post('/validate-boarding', [EscortAuthController::class, 'validateBoarding']);
    Route::post('/validate-temp-card-boarding', [EscortAuthController::class, 'validateTempCardBoarding']);
    Route::post('/onboard-passenger', [EscortAuthController::class, 'onboardPassenger']);
    Route::post('/onboarded-passengers', [EscortAuthController::class, 'getOnboardedPassengers']);
    Route::get('/incident-types', [IncidentApiController::class, 'getIncidentTypes']);
    Route::post('/report-incident', [IncidentApiController::class, 'report']);

    // Future escort-specific endpoints can be added here
    // Route::post('/scan-qr', [EscortController::class, 'scanQr']);
    // Route::get('/onboard-persons', [EscortController::class, 'getOnboardPersons']);
});
