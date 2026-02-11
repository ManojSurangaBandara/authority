<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Incident;
use App\Models\IncidentType;
use App\Models\BusEscortAssignment;
use App\Models\Trip;
use App\Models\Escort;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use GuzzleHttp\Client;

class IncidentApiController extends Controller
{
    /**
     * Get all incident types.
     */
    public function getIncidentTypes()
    {
        $incidentTypes = IncidentType::select('id', 'name')->get();

        return response()->json([
            'success' => true,
            'data' => $incidentTypes
        ]);
    }

    /**
     * Report a new incident.
     */
    public function report(Request $request)
    {

        $escort = Escort::find(JWTAuth::parseToken()->getPayload()->get('escort_id'));

        $validator = Validator::make($request->all(), [
            'incident_type_id' => 'required|exists:incident_types,id',
            'description' => 'required|string|max:1000',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'images' => 'nullable|array|max:3',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120', // max 5MB per image
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }


        // Get escort's current active assignment
        $escortAssignment = BusEscortAssignment::where('escort_id', $escort->id)
            ->active()
            ->first();

        if (!$escortAssignment) {
            return response()->json([
                'success' => false,
                'message' => 'No active assignment found for this escort'
            ], 400);
        }

        // Determine the route ID based on route type
        $routeId = $escortAssignment->route_type === 'living_out'
            ? $escortAssignment->bus_route_id
            : $escortAssignment->living_in_bus_id;

        // Determine current time period (morning: before 12 PM, evening: 12 PM and after)
        $now = now();
        $isMorning = $now->hour < 12;
        $startOfDay = $now->copy()->startOfDay();

        if ($isMorning) {
            $periodStart = $startOfDay;
            $periodEnd = $startOfDay->copy()->setHour(11)->setMinute(59)->setSecond(59);
        } else {
            $periodStart = $startOfDay->copy()->setHour(12)->setMinute(0)->setSecond(0);
            $periodEnd = $startOfDay->copy()->endOfDay();
        }

        // Find the current active trip for this route and time period
        $currentTrip = Trip::where('bus_route_id', $routeId)
            ->where('route_type', $escortAssignment->route_type)
            ->whereBetween('trip_start_time', [$periodStart, $periodEnd])
            ->whereNull('trip_end_time')
            ->first();

        // Incident reporting is only allowed during valid ongoing trips
        if (!$currentTrip) {
            return response()->json([
                'success' => false,
                'message' => 'Incident reporting is only allowed during active trips. Please start a trip first.'
            ], 400);
        }

        // Get assignment details from the current trip
        $busRouteId = null;
        $routeType = $escortAssignment->route_type;

        if ($routeType === 'living_out') {
            $busRouteId = $escortAssignment->bus_route_id;
        } elseif ($routeType === 'living_in') {
            $busRouteId = $escortAssignment->living_in_bus_id;
        }

        $imagePaths = [];
        if ($request->hasFile('images')) {
            // Get the web server URL from environment, default to localhost for development
            $webServerUrl = env('WEB_SERVER_URL', 'http://127.0.0.1:8000');

            // Check if the web server is on localhost to avoid HTTP calls to self
            // (Web server and API server are the same)
            if (in_array($webServerUrl, ['http://localhost', 'http://127.0.0.1', 'http://127.0.0.1:8000', 'https://testappv2.army.lk/ahqams'])) {
                // On localhost, store images directly in local storage
                foreach ($request->file('images') as $image) {
                    $path = $image->store('incidents', 'public');
                    $imagePaths[] = $path;
                }
            } else {
                // On Live api server, upload images via HTTP to the web server
                // (API code is hosted in a seperate server)
                $client = new Client(['timeout' => 10]);
                foreach ($request->file('images') as $image) {
                    try {
                        $response = $client->post($webServerUrl . '/api/internal/upload-incident-image', [
                            'multipart' => [
                                [
                                    'name' => 'image',
                                    'contents' => fopen($image->getPathname(), 'r'),
                                    'filename' => $image->getClientOriginalName(),
                                ]
                            ]
                        ]);
                        $data = json_decode($response->getBody(), true);
                        if ($data['success']) {
                            $imagePaths[] = $data['path'];
                        }
                    } catch (\Exception $e) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Failed to upload image to web server: ' . $e->getMessage()
                        ], 500);
                    }
                }
            }
        }

        $incident = Incident::create([
            'incident_type_id' => $request->incident_type_id,
            'trip_id' => $currentTrip->id, // Always link to the active trip
            'description' => $request->description,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'image1' => $imagePaths[0] ?? null,
            'image2' => $imagePaths[1] ?? null,
            'image3' => $imagePaths[2] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Incident reported successfully',
            'data' => $incident
        ], 201);
    }

    /**
     * Upload incident image (internal use by API server)
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        $path = $request->file('image')->store('incidents', 'public');

        return response()->json([
            'success' => true,
            'path' => $path
        ]);
    }
}
