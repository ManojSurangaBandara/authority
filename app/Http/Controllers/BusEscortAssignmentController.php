<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\DataTables\BusEscortAssignmentDataTable;
use App\Models\BusEscortAssignment;
use App\Models\BusRoute;
use App\Models\Escort;
use App\Models\LivingInBuses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class BusEscortAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all assignments with their routes and escorts
        $allAssignments = BusEscortAssignment::with(['escort', 'busRoute.assignedBus', 'livingInBus.assignedBus'])
            ->where('status', 'active')
            ->get();

        // Get all routes (for backwards compatibility with view)
        $routes = BusRoute::with(['bus', 'escortAssignment.escort'])->get();

        // Get available escorts (not currently assigned to active routes)
        $assignedEscortIds = BusEscortAssignment::where('status', 'active')
            ->whereNotNull('escort_id')
            ->pluck('escort_id')
            ->toArray();
        $availableEscorts = Escort::whereNotIn('id', $assignedEscortIds)->get();

        // Get unassigned routes (both living out and living in)
        $unassignedRoutes = collect();

        // Get living out routes without active escort assignments
        $unassignedLivingOutRoutes = BusRoute::with('bus')
            ->whereDoesntHave('escortAssignment', function ($query) {
                $query->where('status', 'active')->where('route_type', 'living_out');
            })
            ->get();

        // Get living in routes without active escort assignments
        $assignedLivingInRouteIds = BusEscortAssignment::where('status', 'active')
            ->where('route_type', 'living_in')
            ->pluck('living_in_bus_id')
            ->toArray();
        $unassignedLivingInRoutes = LivingInBuses::whereNotIn('id', $assignedLivingInRouteIds)->get();

        // Add living out routes to collection
        foreach ($unassignedLivingOutRoutes as $route) {
            $unassignedRoutes->push((object) [
                'id' => $route->id,
                'name' => $route->name,
                'type' => 'living_out',
                'display_name' => $route->name . ' (Living Out)',
                'bus' => $route->bus
            ]);
        }

        // Add living in routes to collection
        foreach ($unassignedLivingInRoutes as $route) {
            $unassignedRoutes->push((object) [
                'id' => $route->id,
                'name' => $route->name,
                'type' => 'living_in',
                'display_name' => $route->name . ' (Living In)',
                'bus' => null
            ]);
        }

        return view('bus-escort-assignments.index', compact('allAssignments', 'routes', 'availableEscorts', 'unassignedRoutes'));
    }



    /**
     * Display the specified resource.
     */


    /**
     * Assign an escort to a route
     */
    public function assign(Request $request)
    {
        $request->validate([
            'escort_id' => 'required|exists:escorts,id',
            'route_id' => 'required',
            'route_type' => 'required|in:living_out,living_in',
        ]);

        $escort = Escort::findOrFail($request->escort_id);

        // Validate and get route based on type
        if ($request->route_type === 'living_out') {
            $route = BusRoute::findOrFail($request->route_id);
            $routeName = $route->name;
        } else {
            $route = LivingInBuses::findOrFail($request->route_id);
            $routeName = $route->name;
        }

        // Check if escort is already assigned to an active route
        $existingEscortAssignment = BusEscortAssignment::where('escort_id', $request->escort_id)
            ->where('status', 'active')
            ->first();
        if ($existingEscortAssignment) {
            $escortName = ($escort->rank ?? 'N/A') . ' ' . ($escort->name ?? 'Unknown');
            $existingRouteName = $existingEscortAssignment->route_name ?? 'Unknown Route';
            return response()->json([
                'success' => false,
                'message' => "Escort {$escortName} is already assigned to route {$existingRouteName}."
            ]);
        }

        // Check if route already has an active escort based on route type
        $query = BusEscortAssignment::where('status', 'active');
        if ($request->route_type === 'living_out') {
            $query->where('bus_route_id', $request->route_id)->where('route_type', 'living_out');
        } else {
            $query->where('living_in_bus_id', $request->route_id)->where('route_type', 'living_in');
        }

        $existingRouteAssignment = $query->first();
        if ($existingRouteAssignment) {
            $existingEscort = Escort::find($existingRouteAssignment->escort_id);
            $existingEscortName = $existingEscort ? (($existingEscort->rank ?? 'N/A') . ' ' . ($existingEscort->name ?? 'Unknown')) : 'Unknown Escort';
            return response()->json([
                'success' => false,
                'message' => "Route {$routeName} already has escort {$existingEscortName} assigned."
            ]);
        }

        // Create the assignment
        $assignmentData = [
            'route_type' => $request->route_type,
            'escort_id' => $request->escort_id,
            'assigned_date' => now()->format('Y-m-d'),
            'end_date' => null,
            'status' => 'active',
            'created_by' => Auth::user()->name ?? 'System'
        ];

        // Set the correct route ID based on route type
        if ($request->route_type === 'living_out') {
            $assignmentData['bus_route_id'] = $request->route_id;
        } else {
            $assignmentData['living_in_bus_id'] = $request->route_id;
        }

        BusEscortAssignment::create($assignmentData);

        $escortName = ($escort->rank ?? 'N/A') . ' ' . ($escort->name ?? 'Unknown');
        return response()->json([
            'success' => true,
            'message' => "Escort {$escortName} has been successfully assigned to route {$routeName}."
        ]);
    }

    /**
     * Unassign an escort from a route
     */
    public function unassign(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|exists:bus_escort_assignments,id',
        ]);

        $assignment = BusEscortAssignment::with(['escort', 'busRoute'])->findOrFail($request->assignment_id);

        if ($assignment->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => "Assignment is not currently active."
            ]);
        }

        // Get escort and route info before updating (in case relationships become null)
        $escortName = $assignment->escort ? ($assignment->escort->rank ?? 'N/A') . ' ' . ($assignment->escort->name ?? 'Unknown') : 'Unknown Escort';
        $routeName = $assignment->busRoute ? $assignment->busRoute->name : 'Unknown Route';

        // Check if there's already an inactive assignment for this route
        $existingInactive = BusEscortAssignment::where('bus_route_id', $assignment->bus_route_id)
            ->where('status', 'inactive')
            ->where('id', '!=', $assignment->id)
            ->first();

        if ($existingInactive) {
            // Delete the old inactive assignment to avoid constraint violation
            $existingInactive->forceDelete();
        }

        // Mark as inactive instead of deleting
        $assignment->update([
            'status' => 'inactive',
            'end_date' => now()->format('Y-m-d')
        ]);

        return response()->json([
            'success' => true,
            'message' => "Escort {$escortName} has been successfully unassigned from route {$routeName}."
        ]);
    }

    /**
     * Get assignment data for AJAX requests
     */
    public function getAssignmentData()
    {
        $routes = BusRoute::with(['bus', 'escortAssignment.escort'])->get();
        $availableEscorts = Escort::whereDoesntHave('escortAssignments', function ($query) {
            $query->where('status', 'active')->whereNotNull('escort_id');
        })->get();

        return response()->json([
            'routes' => $routes,
            'availableEscorts' => $availableEscorts
        ]);
    }
}
