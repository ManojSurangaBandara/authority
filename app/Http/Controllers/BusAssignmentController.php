<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bus;
use App\Models\BusRoute;
use App\Models\LivingInBuses;
use App\Models\BusRouteAssignment;
use Illuminate\Support\Facades\DB;

class BusAssignmentController extends Controller
{
    /**
     * Display the bus assignment page
     */
    public function index()
    {
        // Get all buses with their active route assignments
        $buses = Bus::with(['activeRouteAssignment'])->get();

        // Get all route assignments with their buses for display
        $assignments = BusRouteAssignment::with('bus')->active()->get();

        // Get unassigned buses (buses that don't have active route assignments)
        $unassignedBuses = Bus::whereDoesntHave('routeAssignments', function ($query) {
            $query->where('status', 'active');
        })->get();

        // Get all available routes for assignment
        $unassignedRoutes = collect();

        // Get living out routes that are not assigned
        $assignedLivingOutRouteIds = BusRouteAssignment::active()
            ->where('route_type', 'living_out')
            ->pluck('route_id');
        $unassignedLivingOutRoutes = BusRoute::whereNotIn('id', $assignedLivingOutRouteIds)->get();

        // Get living in routes that are not assigned
        $assignedLivingInRouteIds = BusRouteAssignment::active()
            ->where('route_type', 'living_in')
            ->pluck('route_id');
        $unassignedLivingInRoutes = LivingInBuses::whereNotIn('id', $assignedLivingInRouteIds)->get();

        // Add living out routes to dropdown
        foreach ($unassignedLivingOutRoutes as $route) {
            $unassignedRoutes->push((object) [
                'id' => $route->id,
                'name' => $route->name,
                'type' => 'living_out',
                'display_name' => $route->name . ' (Living Out)'
            ]);
        }

        // Add living in routes to dropdown
        foreach ($unassignedLivingInRoutes as $route) {
            $unassignedRoutes->push((object) [
                'id' => $route->id,
                'name' => $route->name,
                'type' => 'living_in',
                'display_name' => $route->name . ' (Living In)'
            ]);
        }

        return view('bus-assignments.index', compact('buses', 'assignments', 'unassignedBuses', 'unassignedRoutes'));
    }

    /**
     * Assign a bus to a route
     */
    public function assign(Request $request)
    {
        $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'route_id' => 'required',
            'route_type' => 'required|in:living_out,living_in',
        ]);

        $bus = Bus::findOrFail($request->bus_id);

        // Validate route exists based on type
        if ($request->route_type === 'living_out') {
            $route = BusRoute::findOrFail($request->route_id);
        } else {
            $route = LivingInBuses::findOrFail($request->route_id);
        }

        // Check if bus is already assigned to another route
        $existingAssignment = BusRouteAssignment::where('bus_id', $request->bus_id)
            ->where('status', 'active')
            ->first();

        if ($existingAssignment) {
            $existingRouteName = $existingAssignment->route_name;
            return response()->json([
                'success' => false,
                'message' => "Bus {$bus->name} is already assigned to route '{$existingRouteName}'."
            ]);
        }

        // Check if route is already assigned to another bus
        $existingRouteAssignment = BusRouteAssignment::where('route_id', $request->route_id)
            ->where('route_type', $request->route_type)
            ->where('status', 'active')
            ->first();

        if ($existingRouteAssignment) {
            $existingBus = Bus::find($existingRouteAssignment->bus_id);
            return response()->json([
                'success' => false,
                'message' => "Route {$route->name} is already assigned to bus {$existingBus->name}."
            ]);
        }

        // Create new assignment
        BusRouteAssignment::create([
            'bus_id' => $request->bus_id,
            'route_id' => $request->route_id,
            'route_type' => $request->route_type,
            'status' => 'active'
        ]);

        return response()->json([
            'success' => true,
            'message' => "Bus {$bus->name} has been successfully assigned to route {$route->name} ({$request->route_type})."
        ]);
    }

    /**
     * Unassign a bus from a route
     */
    public function unassign(Request $request)
    {
        try {
            $request->validate([
                'assignment_id' => 'required|exists:bus_route_assignments,id',
            ]);

            $assignment = BusRouteAssignment::findOrFail($request->assignment_id);
            $bus = $assignment->bus;
            $routeName = $assignment->route_name;

            // Deactivate the assignment
            $assignment->update(['status' => 'inactive']);

            return response()->json([
                'success' => true,
                'message' => "Bus {$bus->name} has been successfully unassigned from route {$routeName}."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while unassigning the bus: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get assignment data for AJAX calls
     */
    public function getAssignmentData()
    {
        $assignments = BusRouteAssignment::with('bus')->active()->get();

        $data = $assignments->map(function ($assignment) {
            return [
                'id' => $assignment->id,
                'bus_id' => $assignment->bus_id,
                'bus_name' => $assignment->bus->name ?? 'Unknown Bus',
                'bus_no' => $assignment->bus->no ?? 'N/A',
                'route_id' => $assignment->route_id,
                'route_name' => $assignment->route_name,
                'route_type' => $assignment->route_type,
                'route_type_display' => ucfirst(str_replace('_', ' ', $assignment->route_type))
            ];
        });

        return response()->json($data);
    }
}
