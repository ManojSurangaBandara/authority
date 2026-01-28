<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Incident;
use App\Models\IncidentType;
use App\Models\BusRoute;
use App\Models\LivingInBus;
use App\Models\BusEscortAssignment;
use App\Models\BusDriverAssignment;
use App\Models\BusRouteAssignment;
use App\Models\SlcmpInchargeAssignment;
use App\Models\Escort;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

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
            ->where('status', 'active')
            ->first();

        if (!$escortAssignment) {
            return response()->json([
                'success' => false,
                'message' => 'No active assignment found for this escort'
            ], 400);
        }

        // Get assignment details
        $busRouteId = null;
        $routeType = $escortAssignment->route_type;

        if ($routeType === 'living_out') {
            $busRouteId = $escortAssignment->bus_route_id;
        } elseif ($routeType === 'living_in') {
            $busRouteId = $escortAssignment->living_in_bus_id;
        }

        // Get driver assignment for the same route
        $driverId = null;
        $busId = null;
        $slcmpInchargeId = null;

        if ($routeType === 'living_out') {
            $driverAssignment = BusDriverAssignment::where('bus_route_id', $busRouteId)
                ->where('route_type', 'living_out')
                ->where('status', 'active')
                ->first();

            $busRouteAssignment = BusRouteAssignment::where('route_id', $busRouteId)
                ->where('route_type', 'living_out')
                ->where('status', 'active')
                ->first();

            $slcmpAssignment = SlcmpInchargeAssignment::where('bus_route_id', $busRouteId)
                ->where('route_type', 'living_out')
                ->where('status', 'active')
                ->first();
        } elseif ($routeType === 'living_in') {
            $driverAssignment = BusDriverAssignment::where('living_in_bus_id', $busRouteId)
                ->where('route_type', 'living_in')
                ->where('status', 'active')
                ->first();

            $busRouteAssignment = BusRouteAssignment::where('route_id', $busRouteId)
                ->where('route_type', 'living_in')
                ->where('status', 'active')
                ->first();

            $slcmpAssignment = SlcmpInchargeAssignment::where('living_in_bus_id', $busRouteId)
                ->where('route_type', 'living_in')
                ->where('status', 'active')
                ->first();
        }

        if ($driverAssignment) {
            $driverId = $driverAssignment->driver_id;
        }

        if ($busRouteAssignment) {
            $busId = $busRouteAssignment->bus_id;
        }

        if ($slcmpAssignment) {
            $slcmpInchargeId = $slcmpAssignment->slcmp_incharge_id;
        }

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('incidents', 'public');
                $imagePaths[] = $path;
            }
        }

        $incident = Incident::create([
            'incident_type_id' => $request->incident_type_id,
            'description' => $request->description,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'image1' => $imagePaths[0] ?? null,
            'image2' => $imagePaths[1] ?? null,
            'image3' => $imagePaths[2] ?? null,
            'escort_id' => $escort->id,
            'bus_route_id' => $busRouteId,
            'route_type' => $routeType,
            'slcmp_incharge_id' => $slcmpInchargeId,
            'driver_id' => $driverId,
            'bus_id' => $busId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Incident reported successfully',
            'data' => $incident
        ], 201);
    }
}
