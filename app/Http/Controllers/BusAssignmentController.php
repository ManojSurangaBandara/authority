<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bus;
use App\Models\BusRoute;
use Illuminate\Support\Facades\DB;

class BusAssignmentController extends Controller
{
    /**
     * Display the bus assignment page
     */
    public function index()
    {
        // Get all buses with their assigned routes
        $buses = Bus::with(['routes' => function ($query) {
            $query->whereNotNull('bus_id');
        }])->get();

        // Get all routes with their assigned buses
        $routes = BusRoute::with('bus')->get();

        // Get unassigned buses (buses that don't have any routes)
        $unassignedBuses = Bus::whereDoesntHave('routes', function ($query) {
            $query->whereNotNull('bus_id');
        })->get();

        // Get unassigned routes (routes that don't have buses)
        $unassignedRoutes = BusRoute::whereNull('bus_id')->get();

        return view('bus-assignments.index', compact('buses', 'routes', 'unassignedBuses', 'unassignedRoutes'));
    }

    /**
     * Assign a bus to a route
     */
    public function assign(Request $request)
    {
        $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'route_id' => 'required|exists:bus_routes,id',
        ]);

        $bus = Bus::findOrFail($request->bus_id);
        $route = BusRoute::findOrFail($request->route_id);

        // Check if bus is already assigned to another route
        $existingAssignment = BusRoute::where('bus_id', $request->bus_id)->first();
        if ($existingAssignment) {
            return response()->json([
                'success' => false,
                'message' => "Bus {$bus->name} is already assigned to route {$existingAssignment->name}."
            ]);
        }

        // Check if route already has a bus assigned
        if ($route->bus_id) {
            $existingBus = Bus::find($route->bus_id);
            return response()->json([
                'success' => false,
                'message' => "Route {$route->name} is already assigned to bus {$existingBus->name}."
            ]);
        }

        // Assign the bus to the route
        $route->update(['bus_id' => $request->bus_id]);

        return response()->json([
            'success' => true,
            'message' => "Bus {$bus->name} has been successfully assigned to route {$route->name}."
        ]);
    }

    /**
     * Unassign a bus from a route
     */
    public function unassign(Request $request)
    {
        $request->validate([
            'route_id' => 'required|exists:bus_routes,id',
        ]);

        $route = BusRoute::findOrFail($request->route_id);

        if (!$route->bus_id) {
            return response()->json([
                'success' => false,
                'message' => "Route {$route->name} doesn't have any bus assigned."
            ]);
        }

        $bus = Bus::find($route->bus_id);
        $busName = $bus ? $bus->name : 'Unknown Bus';

        // Unassign the bus from the route
        $route->update(['bus_id' => null]);

        return response()->json([
            'success' => true,
            'message' => "Bus {$busName} has been successfully unassigned from route {$route->name}."
        ]);
    }

    /**
     * Get assignment data for AJAX requests
     */
    public function getAssignmentData()
    {
        $buses = Bus::with(['routes' => function ($query) {
            $query->whereNotNull('bus_id');
        }])->get();

        $routes = BusRoute::with('bus')->get();

        return response()->json([
            'buses' => $buses,
            'routes' => $routes
        ]);
    }
}
