<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\DataTables\BusDriverAssignmentDataTable;
use App\Models\BusDriverAssignment;
use App\Models\BusRoute;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class BusDriverAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all routes with their assigned drivers
        $routes = BusRoute::with(['bus', 'driverAssignment.driver'])->get();

        // Get available drivers (not currently assigned to active routes)
        $assignedDriverIds = BusDriverAssignment::where('status', 'active')
            ->whereNotNull('driver_id')
            ->pluck('driver_id')
            ->toArray();
        $availableDrivers = Driver::whereNotIn('id', $assignedDriverIds)->get();

        // Get routes without active driver assignments
        $unassignedRoutes = BusRoute::with('bus')
            ->whereDoesntHave('driverAssignment', function ($query) {
                $query->where('status', 'active');
            })
            ->get();

        return view('bus-driver-assignments.index', compact('routes', 'availableDrivers', 'unassignedRoutes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $busRoutes = BusRoute::with('bus')->get();
        return view('bus-driver-assignments.create', compact('busRoutes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'bus_route_id' => 'required|exists:bus_routes,id',
            'driver_regiment_no' => 'required|string|max:50',
            'driver_rank' => 'required|string|max:100',
            'driver_name' => 'required|string|max:200',
            'driver_contact_no' => 'required|string|max:20',
            'assigned_date' => 'required|date',
            'end_date' => 'nullable|date|after:assigned_date',
            'status' => 'required|in:active,inactive',
        ]);

        // Check if there's already an active assignment for this route
        if ($validatedData['status'] === 'active') {
            $existingAssignment = BusDriverAssignment::where('bus_route_id', $validatedData['bus_route_id'])
                ->where('status', 'active')
                ->first();

            if ($existingAssignment) {
                return back()->withErrors(['bus_route_id' => 'This bus route already has an active driver assignment.'])
                    ->withInput();
            }
        }

        // Check if driver exists by regiment number
        $driver = Driver::where('regiment_no', $request->driver_regiment_no)->first();

        if (!$driver) {
            // Create new driver if doesn't exist
            $driver = Driver::create([
                'regiment_no' => $request->driver_regiment_no,
                'rank' => $request->driver_rank,
                'name' => $request->driver_name,
                'contact_no' => $request->driver_contact_no,
            ]);
        } else {
            // Update existing driver information
            $driver->update([
                'rank' => $request->driver_rank,
                'name' => $request->driver_name,
                'contact_no' => $request->driver_contact_no,
            ]);
        }

        // Create assignment with driver_id instead of individual fields
        $assignmentData = [
            'bus_route_id' => $validatedData['bus_route_id'],
            'driver_id' => $driver->id,
            'assigned_date' => $validatedData['assigned_date'],
            'end_date' => $validatedData['end_date'],
            'status' => $validatedData['status'],
            'created_by' => Auth::user()->name ?? 'System'
        ];

        BusDriverAssignment::create($assignmentData);

        return redirect()->route('bus-driver-assignments.index')
            ->with('success', 'Bus driver assignment created successfully. Driver information has been ' . ($driver->wasRecentlyCreated ? 'created' : 'updated') . ' in the system.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BusDriverAssignment $bus_driver_assignment)
    {
        $bus_driver_assignment->load(['busRoute.bus']);
        return view('bus-driver-assignments.show', compact('bus_driver_assignment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BusDriverAssignment $bus_driver_assignment)
    {
        $busRoutes = BusRoute::with('bus')->get();
        return view('bus-driver-assignments.edit', compact('bus_driver_assignment', 'busRoutes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BusDriverAssignment $bus_driver_assignment)
    {
        $validatedData = $request->validate([
            'bus_route_id' => 'required|exists:bus_routes,id',
            'driver_regiment_no' => 'required|string|max:50',
            'driver_rank' => 'required|string|max:100',
            'driver_name' => 'required|string|max:200',
            'driver_contact_no' => 'required|string|max:20',
            'assigned_date' => 'required|date',
            'end_date' => 'nullable|date|after:assigned_date',
            'status' => 'required|in:active,inactive',
        ]);

        // Check if there's already an active assignment for this route (excluding current assignment)
        if ($validatedData['status'] === 'active') {
            $existingAssignment = BusDriverAssignment::where('bus_route_id', $validatedData['bus_route_id'])
                ->where('status', 'active')
                ->where('id', '!=', $bus_driver_assignment->id)
                ->first();

            if ($existingAssignment) {
                return back()->withErrors(['bus_route_id' => 'This bus route already has an active driver assignment.'])
                    ->withInput();
            }
        }

        // Update or create driver information
        $driver = Driver::where('regiment_no', $request->driver_regiment_no)->first();

        if (!$driver) {
            // Create new driver if doesn't exist
            $driver = Driver::create([
                'regiment_no' => $request->driver_regiment_no,
                'rank' => $request->driver_rank,
                'name' => $request->driver_name,
                'contact_no' => $request->driver_contact_no,
            ]);
        } else {
            // Update existing driver information
            $driver->update([
                'rank' => $request->driver_rank,
                'name' => $request->driver_name,
                'contact_no' => $request->driver_contact_no,
            ]);
        }

        // Update assignment with driver_id instead of individual fields
        $assignmentData = [
            'bus_route_id' => $validatedData['bus_route_id'],
            'driver_id' => $driver->id,
            'assigned_date' => $validatedData['assigned_date'],
            'end_date' => $validatedData['end_date'],
            'status' => $validatedData['status']
        ];

        $bus_driver_assignment->update($assignmentData);

        return redirect()->route('bus-driver-assignments.index')
            ->with('success', 'Bus driver assignment updated successfully. Driver information has been updated in the system.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BusDriverAssignment $bus_driver_assignment)
    {
        $bus_driver_assignment->delete();

        return redirect()->route('bus-driver-assignments.index')
            ->with('success', 'Bus driver assignment deleted successfully.');
    }

    /**
     * Assign a driver to a route
     */
    public function assign(Request $request)
    {
        $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'route_id' => 'required|exists:bus_routes,id',
            'assigned_date' => 'required|date',
            'end_date' => 'nullable|date|after:assigned_date',
        ]);

        $driver = Driver::findOrFail($request->driver_id);
        $route = BusRoute::findOrFail($request->route_id);

        // Check if driver is already assigned to an active route
        $existingDriverAssignment = BusDriverAssignment::where('driver_id', $request->driver_id)
            ->where('status', 'active')
            ->first();
        if ($existingDriverAssignment) {
            $existingRoute = BusRoute::find($existingDriverAssignment->bus_route_id);
            $driverName = ($driver->rank ?? 'N/A') . ' ' . ($driver->name ?? 'Unknown');
            $routeName = $existingRoute ? $existingRoute->name : 'Unknown Route';
            return response()->json([
                'success' => false,
                'message' => "Driver {$driverName} is already assigned to route {$routeName}."
            ]);
        }

        // Check if route already has an active driver
        $existingRouteAssignment = BusDriverAssignment::where('bus_route_id', $request->route_id)
            ->where('status', 'active')
            ->first();
        if ($existingRouteAssignment) {
            $existingDriver = Driver::find($existingRouteAssignment->driver_id);
            $existingDriverName = $existingDriver ? (($existingDriver->rank ?? 'N/A') . ' ' . ($existingDriver->name ?? 'Unknown')) : 'Unknown Driver';
            return response()->json([
                'success' => false,
                'message' => "Route {$route->name} already has driver {$existingDriverName} assigned."
            ]);
        }

        // Create the assignment
        BusDriverAssignment::create([
            'bus_route_id' => $request->route_id,
            'driver_id' => $request->driver_id,
            'assigned_date' => $request->assigned_date,
            'end_date' => $request->end_date,
            'status' => 'active',
            'created_by' => Auth::user()->name ?? 'System'
        ]);

        $driverName = ($driver->rank ?? 'N/A') . ' ' . ($driver->name ?? 'Unknown');
        return response()->json([
            'success' => true,
            'message' => "Driver {$driverName} has been successfully assigned to route {$route->name}."
        ]);
    }

    /**
     * Unassign a driver from a route
     */
    public function unassign(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|exists:bus_driver_assignments,id',
        ]);

        $assignment = BusDriverAssignment::with(['driver', 'busRoute'])->findOrFail($request->assignment_id);

        if ($assignment->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => "Assignment is not currently active."
            ]);
        }

        // Get driver and route info before updating (in case relationships become null)
        $driverName = $assignment->driver ? ($assignment->driver->rank ?? 'N/A') . ' ' . ($assignment->driver->name ?? 'Unknown') : 'Unknown Driver';
        $routeName = $assignment->busRoute ? $assignment->busRoute->name : 'Unknown Route';

        // Check if there's already an inactive assignment for this route
        $existingInactive = BusDriverAssignment::where('bus_route_id', $assignment->bus_route_id)
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
            'message' => "Driver {$driverName} has been successfully unassigned from route {$routeName}."
        ]);
    }

    /**
     * Get assignment data for AJAX requests
     */
    public function getAssignmentData()
    {
        $routes = BusRoute::with(['bus', 'driverAssignment.driver'])->get();
        $availableDrivers = Driver::whereDoesntHave('driverAssignments', function ($query) {
            $query->where('status', 'active')->whereNotNull('driver_id');
        })->get();

        return response()->json([
            'routes' => $routes,
            'availableDrivers' => $availableDrivers
        ]);
    }

    /**
     * Get driver details from Strength Management System API
     */
    public function getDriverDetails(Request $request)
    {
        $regimentNo = $request->input('regiment_no');

        if (empty($regimentNo)) {
            return response()->json(['success' => false, 'message' => 'Regiment number is required'], 400);
        }

        try {
            // Call the actual Army API endpoint
            $apiToken = '1189d8dde195a36a9c4a721a390a74e6';
            $apiUrl = "https://str.army.lk/api/get_person/?str-token={$apiToken}&service_no={$regimentNo}";

            $response = Http::timeout(10)->get($apiUrl);

            if ($response->successful()) {
                // The response comes as a JSON array with brackets, we need to decode it
                $responseData = json_decode($response->body(), true);

                // Check if the API returned valid data
                if (is_array($responseData) && !empty($responseData)) {
                    // Extract the first record from the array
                    $data = $responseData[0];

                    // Map API response fields to our application fields
                    $driverData = [
                        'rank' => $data['rank'] ?? '',
                        'name' => $data['name'] ?? '',
                        // Since contact_no is not directly available in the API response,
                        // we'll leave it empty for the user to fill in
                        'contact_no' => ''
                    ];

                    return response()->json([
                        'success' => true,
                        'data' => $driverData
                    ]);
                }

                return response()->json(['success' => false, 'message' => 'No data found for this regiment number'], 404);
            }

            return response()->json(['success' => false, 'message' => 'Failed to fetch data from Army API'], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching driver details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get bus details when route is selected
     */
    public function getBusDetails(Request $request)
    {
        $request->validate([
            'bus_route_id' => 'required|exists:bus_routes,id'
        ]);

        $busRoute = BusRoute::with('bus')->find($request->bus_route_id);

        if ($busRoute && $busRoute->bus) {
            return response()->json([
                'success' => true,
                'data' => [
                    'bus_no' => $busRoute->bus->no,
                    'bus_name' => $busRoute->bus->name,
                    'bus_type' => $busRoute->bus->type->name ?? 'N/A'
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Bus details not found for this route.'
        ], 404);
    }
}
