<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\DataTables\SlcmpInchargeAssignmentDataTable;
use App\Models\SlcmpInchargeAssignment;
use App\Models\BusRoute;
use App\Models\LivingInBuses;
use App\Models\BusRouteAssignment;
use App\Models\SlcmpIncharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class SlcmpInchargeAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all routes (both living_out and living_in) with their assigned SLCMP in-charges
        $livingOutRoutes = BusRoute::with(['bus', 'slcmpInchargeAssignment.slcmpIncharge'])->get();
        $livingInRoutes = LivingInBuses::with(['slcmpInchargeAssignment.slcmpIncharge'])->get();

        // Combine routes with type indicators
        $routes = collect();
        foreach ($livingOutRoutes as $route) {
            $route->route_type = 'living_out';
            $route->display_name = $route->name . ' (Living Out)' . ($route->bus ? ' - ' . $route->bus->name . ' (' . $route->bus->no . ')' : '');
            $routes->push($route);
        }
        foreach ($livingInRoutes as $route) {
            $route->route_type = 'living_in';
            $route->display_name = $route->name . ' (Living In)';
            $routes->push($route);
        }

        // Get available SLCMP in-charges (not currently assigned to active routes)
        $assignedSlcmpIds = SlcmpInchargeAssignment::where('status', 'active')
            ->whereNotNull('slcmp_incharge_id')
            ->pluck('slcmp_incharge_id')
            ->toArray();
        $availableSlcmpIncharges = SlcmpIncharge::whereNotIn('id', $assignedSlcmpIds)->get();

        // Get routes without active SLCMP in-charge assignments
        $unassignedLivingOutRoutes = BusRoute::with('bus')
            ->whereDoesntHave('slcmpInchargeAssignment', function ($query) {
                $query->where('status', 'active');
            })
            ->get();

        $unassignedLivingInRoutes = LivingInBuses::whereDoesntHave('slcmpInchargeAssignment', function ($query) {
            $query->where('status', 'active');
        })
            ->get();

        // Combine unassigned routes
        $unassignedRoutes = collect();
        foreach ($unassignedLivingOutRoutes as $route) {
            $route->route_type = 'living_out';
            $route->display_name = $route->name . ' (Living Out)' . ($route->bus ? ' - ' . $route->bus->name . ' (' . $route->bus->no . ')' : '');
            $unassignedRoutes->push($route);
        }
        foreach ($unassignedLivingInRoutes as $route) {
            $route->route_type = 'living_in';
            $route->display_name = $route->name . ' (Living In)';
            $unassignedRoutes->push($route);
        }

        return view('slcmp-incharge-assignments.index', compact('routes', 'availableSlcmpIncharges', 'unassignedRoutes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $busRoutes = BusRoute::with('bus.type')->get();
        $livingInRoutes = LivingInBuses::with('bus.type')->get();
        return view('slcmp-incharge-assignments.create', compact('busRoutes', 'livingInRoutes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'route_type' => 'required|in:living_out,living_in',
            'route_id' => 'required|integer',
            'slcmp_regiment_no' => 'required|string|max:50',
            'slcmp_rank' => 'required|string|max:100',
            'slcmp_name' => 'required|string|max:200',
            'slcmp_contact_no' => 'required|string|max:20',
            'assigned_date' => 'required|date',
            'end_date' => 'nullable|date|after:assigned_date',
            'status' => 'required|in:active,inactive',
        ]);

        // Validate route exists based on type
        if ($validatedData['route_type'] === 'living_out') {
            $route = BusRoute::findOrFail($validatedData['route_id']);
        } else {
            $route = LivingInBuses::findOrFail($validatedData['route_id']);
        }

        // Check if there's already an active assignment for this route
        if ($validatedData['status'] === 'active') {
            $existingAssignment = SlcmpInchargeAssignment::where('route_id', $validatedData['route_id'])
                ->where('route_type', $validatedData['route_type'])
                ->where('status', 'active')
                ->first();

            if ($existingAssignment) {
                return back()->withErrors(['route_id' => 'This route already has an active SLCMP assignment.'])
                    ->withInput();
            }
        }

        // Check if SLCMP in-charge exists by regiment number
        $slcmpIncharge = SlcmpIncharge::where('regiment_no', $request->slcmp_regiment_no)->first();

        if (!$slcmpIncharge) {
            // Create new SLCMP in-charge if doesn't exist
            $slcmpIncharge = SlcmpIncharge::create([
                'regiment_no' => $request->slcmp_regiment_no,
                'rank' => $request->slcmp_rank,
                'name' => $request->slcmp_name,
                'contact_no' => $request->slcmp_contact_no,
            ]);
        } else {
            // Update existing SLCMP in-charge information
            $slcmpIncharge->update([
                'rank' => $request->slcmp_rank,
                'name' => $request->slcmp_name,
                'contact_no' => $request->slcmp_contact_no,
            ]);
        }

        // Create assignment with new route fields
        $assignmentData = [
            'route_id' => $validatedData['route_id'],
            'route_type' => $validatedData['route_type'],
            'living_in_bus_id' => $validatedData['route_type'] === 'living_in' ? $validatedData['route_id'] : null,
            'slcmp_incharge_id' => $slcmpIncharge->id,
            'assigned_date' => $validatedData['assigned_date'],
            'end_date' => $validatedData['end_date'],
            'status' => $validatedData['status'],
            'created_by' => Auth::user()->name ?? 'System'
        ];

        // Set the appropriate foreign key based on route type
        if ($validatedData['route_type'] === 'living_out') {
            $assignmentData['bus_route_id'] = $validatedData['route_id'];
        }

        SlcmpInchargeAssignment::create($assignmentData);

        return redirect()->route('slcmp-incharge-assignments.index')
            ->with('success', 'SLCMP in-charge assignment created successfully. SLCMP in-charge information has been ' . ($slcmpIncharge->wasRecentlyCreated ? 'created' : 'updated') . ' in the system.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SlcmpInchargeAssignment $slcmp_incharge_assignment)
    {
        $slcmp_incharge_assignment->load(['route.bus.type', 'livingInBus.bus.type']);
        $slcmpInchargeAssignment = $slcmp_incharge_assignment;
        return view('slcmp-incharge-assignments.show', compact('slcmpInchargeAssignment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SlcmpInchargeAssignment $slcmp_incharge_assignment)
    {
        $busRoutes = BusRoute::with('bus.type')->get();
        $livingInRoutes = LivingInBuses::with('bus.type')->get();
        $slcmp_incharge_assignment->load(['route.bus.type', 'livingInBus.bus.type']);
        $slcmpInchargeAssignment = $slcmp_incharge_assignment;
        return view('slcmp-incharge-assignments.edit', compact('slcmpInchargeAssignment', 'busRoutes', 'livingInRoutes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SlcmpInchargeAssignment $slcmp_incharge_assignment)
    {
        $validatedData = $request->validate([
            'route_type' => 'required|in:living_out,living_in',
            'route_id' => 'required|integer',
            'slcmp_regiment_no' => 'required|string|max:50',
            'slcmp_rank' => 'required|string|max:100',
            'slcmp_name' => 'required|string|max:200',
            'slcmp_contact_no' => 'required|string|max:20',
            'assigned_date' => 'required|date',
            'end_date' => 'nullable|date|after:assigned_date',
            'status' => 'required|in:active,inactive',
        ]);

        // Validate route exists based on type
        if ($validatedData['route_type'] === 'living_out') {
            $route = BusRoute::findOrFail($validatedData['route_id']);
        } else {
            $route = LivingInBuses::findOrFail($validatedData['route_id']);
        }

        // Check if there's already an active assignment for this route (excluding current assignment)
        if ($validatedData['status'] === 'active') {
            $existingAssignment = SlcmpInchargeAssignment::where('route_id', $validatedData['route_id'])
                ->where('route_type', $validatedData['route_type'])
                ->where('status', 'active')
                ->where('id', '!=', $slcmp_incharge_assignment->id)
                ->first();

            if ($existingAssignment) {
                return back()->withErrors(['route_id' => 'This route already has an active SLCMP assignment.'])
                    ->withInput();
            }
        }

        // Update or create SLCMP in-charge information
        $slcmpIncharge = SlcmpIncharge::where('regiment_no', $request->slcmp_regiment_no)->first();

        if (!$slcmpIncharge) {
            // Create new SLCMP in-charge if doesn't exist
            $slcmpIncharge = SlcmpIncharge::create([
                'regiment_no' => $request->slcmp_regiment_no,
                'rank' => $request->slcmp_rank,
                'name' => $request->slcmp_name,
                'contact_no' => $request->slcmp_contact_no,
            ]);
        } else {
            // Update existing SLCMP in-charge information
            $slcmpIncharge->update([
                'rank' => $request->slcmp_rank,
                'name' => $request->slcmp_name,
                'contact_no' => $request->slcmp_contact_no,
            ]);
        }

        // Update assignment with new route fields
        $assignmentData = [
            'route_id' => $validatedData['route_id'],
            'route_type' => $validatedData['route_type'],
            'living_in_bus_id' => $validatedData['route_type'] === 'living_in' ? $validatedData['route_id'] : null,
            'slcmp_incharge_id' => $slcmpIncharge->id,
            'assigned_date' => $validatedData['assigned_date'],
            'end_date' => $validatedData['end_date'],
            'status' => $validatedData['status']
        ];

        // Set the appropriate foreign key based on route type
        if ($validatedData['route_type'] === 'living_out') {
            $assignmentData['bus_route_id'] = $validatedData['route_id'];
        }

        $slcmp_incharge_assignment->update($assignmentData);

        return redirect()->route('slcmp-incharge-assignments.index')
            ->with('success', 'SLCMP in-charge assignment updated successfully. SLCMP in-charge information has been ' . ($slcmpIncharge->wasRecentlyCreated ? 'created' : 'updated') . ' in the system.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SlcmpInchargeAssignment $slcmp_incharge_assignment)
    {
        $slcmp_incharge_assignment->delete();

        return redirect()->route('slcmp-incharge-assignments.index')
            ->with('success', 'SLCMP in-charge assignment deleted successfully.');
    }

    /**
     * Assign a SLCMP in-charge to a route
     */
    public function assign(Request $request)
    {
        $request->validate([
            'slcmp_incharge_id' => 'required|exists:slcmp_incharges,id',
            'route_type' => 'required|in:living_out,living_in',
            'route_id' => 'required|integer',
            'assigned_date' => 'required|date',
            'end_date' => 'nullable|date|after:assigned_date',
        ]);

        // Validate route exists based on type
        if ($request->route_type === 'living_out') {
            $route = BusRoute::findOrFail($request->route_id);
        } else {
            $route = LivingInBuses::findOrFail($request->route_id);
        }

        $slcmpIncharge = SlcmpIncharge::findOrFail($request->slcmp_incharge_id);

        // Check if SLCMP in-charge is already assigned to an active route
        $existingSlcmpAssignment = SlcmpInchargeAssignment::where('slcmp_incharge_id', $request->slcmp_incharge_id)
            ->where('status', 'active')
            ->first();
        if ($existingSlcmpAssignment) {
            $existingRoute = $existingSlcmpAssignment->route;
            $slcmpName = ($slcmpIncharge->rank ?? 'N/A') . ' ' . ($slcmpIncharge->name ?? 'Unknown');
            $routeName = $existingRoute ? $existingRoute->name : 'Unknown Route';
            return response()->json([
                'success' => false,
                'message' => "SLCMP In-charge {$slcmpName} is already assigned to route {$routeName}."
            ]);
        }

        // Check if route already has an active SLCMP in-charge
        $existingRouteAssignment = SlcmpInchargeAssignment::where('route_id', $request->route_id)
            ->where('route_type', $request->route_type)
            ->where('status', 'active')
            ->first();
        if ($existingRouteAssignment) {
            $existingSlcmp = SlcmpIncharge::find($existingRouteAssignment->slcmp_incharge_id);
            $existingSlcmpName = $existingSlcmp ? (($existingSlcmp->rank ?? 'N/A') . ' ' . ($existingSlcmp->name ?? 'Unknown')) : 'Unknown SLCMP In-charge';
            return response()->json([
                'success' => false,
                'message' => "Route {$route->name} already has SLCMP in-charge {$existingSlcmpName} assigned."
            ]);
        }

        // Create the assignment
        $assignmentData = [
            'route_id' => $request->route_id,
            'route_type' => $request->route_type,
            'living_in_bus_id' => $request->route_type === 'living_in' ? $request->route_id : null,
            'slcmp_incharge_id' => $request->slcmp_incharge_id,
            'assigned_date' => $request->assigned_date,
            'end_date' => $request->end_date,
            'status' => 'active',
            'created_by' => Auth::user()->name ?? 'System'
        ];

        // Set the appropriate foreign key based on route type
        if ($request->route_type === 'living_out') {
            $assignmentData['bus_route_id'] = $request->route_id;
        }

        SlcmpInchargeAssignment::create($assignmentData);

        $slcmpName = ($slcmpIncharge->rank ?? 'N/A') . ' ' . ($slcmpIncharge->name ?? 'Unknown');
        return response()->json([
            'success' => true,
            'message' => "SLCMP In-charge {$slcmpName} has been successfully assigned to route {$route->name}."
        ]);
    }

    /**
     * Unassign a SLCMP in-charge from a route
     */
    public function unassign(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|exists:slcmp_incharge_assignments,id',
        ]);

        $assignment = SlcmpInchargeAssignment::with(['slcmpIncharge', 'route', 'livingInBus'])->findOrFail($request->assignment_id);

        if ($assignment->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => "Assignment is not currently active."
            ]);
        }

        // Get SLCMP in-charge and route info before updating (in case relationships become null)
        $slcmpName = $assignment->slcmpIncharge ? ($assignment->slcmpIncharge->rank ?? 'N/A') . ' ' . ($assignment->slcmpIncharge->name ?? 'Unknown') : 'Unknown SLCMP In-charge';
        $routeName = $assignment->route ? $assignment->route->name : 'Unknown Route';

        // Check if there's already an inactive assignment for this route
        $existingInactive = SlcmpInchargeAssignment::where('route_id', $assignment->route_id)
            ->where('route_type', $assignment->route_type)
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
            'message' => "SLCMP In-charge {$slcmpName} has been successfully unassigned from route {$routeName}."
        ]);
    }

    /**
     * Get assignment data for AJAX requests
     */
    public function getAssignmentData()
    {
        // Get all routes (both living_out and living_in) with their assigned SLCMP in-charges
        $livingOutRoutes = BusRoute::with(['bus', 'slcmpInchargeAssignment.slcmpIncharge'])->get();
        $livingInRoutes = LivingInBuses::with(['slcmpInchargeAssignment.slcmpIncharge'])->get();

        // Combine routes with type indicators
        $routes = collect();
        foreach ($livingOutRoutes as $route) {
            $route->route_type = 'living_out';
            $routes->push($route);
        }
        foreach ($livingInRoutes as $route) {
            $route->route_type = 'living_in';
            $routes->push($route);
        }

        $availableSlcmpIncharges = SlcmpIncharge::whereDoesntHave('slcmpInchargeAssignments', function ($query) {
            $query->where('status', 'active')->whereNotNull('slcmp_incharge_id');
        })->get();

        return response()->json([
            'routes' => $routes,
            'availableSlcmpIncharges' => $availableSlcmpIncharges
        ]);
    }

    /**
     * Get SLCMP details from Strength Management System API
     */
    public function getSlcmpDetails(Request $request)
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
                    $slcmpData = [
                        'rank' => $data['rank'] ?? '',
                        'name' => $data['name'] ?? '',
                        // Since contact_no is not directly available in the API response,
                        // we'll leave it empty for the user to fill in
                        'contact_no' => ''
                    ];

                    return response()->json([
                        'success' => true,
                        'data' => $slcmpData
                    ]);
                }

                return response()->json(['success' => false, 'message' => 'No data found for this regiment number'], 404);
            }

            return response()->json(['success' => false, 'message' => 'Failed to fetch data from Army API'], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching SLCMP details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get bus details when route is selected
     */
    public function getBusDetails(Request $request)
    {
        $request->validate([
            'route_type' => 'required|in:living_out,living_in',
            'route_id' => 'required|integer'
        ]);

        if ($request->route_type === 'living_out') {
            $route = BusRoute::with('bus.type')->find($request->route_id);
            if ($route && $route->bus) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'bus_no' => $route->bus->no,
                        'bus_name' => $route->bus->name,
                        'bus_type' => $route->bus->type->name ?? 'N/A'
                    ]
                ]);
            }
        } else {
            // For living in routes, get bus details from BusRouteAssignment
            $assignment = BusRouteAssignment::with('bus.type')
                ->where('route_id', $request->route_id)
                ->where('route_type', 'living_in')
                ->where('status', 'active')
                ->first();

            if ($assignment && $assignment->bus) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'bus_no' => $assignment->bus->no,
                        'bus_name' => $assignment->bus->name,
                        'bus_type' => $assignment->bus->type->name ?? 'N/A'
                    ]
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Bus details not found for this route.'
        ], 404);
    }
}
