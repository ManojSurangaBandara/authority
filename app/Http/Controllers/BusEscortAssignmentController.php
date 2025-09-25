<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\DataTables\BusEscortAssignmentDataTable;
use App\Models\BusEscortAssignment;
use App\Models\BusRoute;
use App\Models\Escort;
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
        // Get all routes with their assigned escorts
        $routes = BusRoute::with(['bus', 'escortAssignment.escort'])->get();

        // Get available escorts (not currently assigned to active routes)
        $assignedEscortIds = BusEscortAssignment::where('status', 'active')
            ->whereNotNull('escort_id')
            ->pluck('escort_id')
            ->toArray();
        $availableEscorts = Escort::whereNotIn('id', $assignedEscortIds)->get();

        // Get routes without active escort assignments
        $unassignedRoutes = BusRoute::with('bus')
            ->whereDoesntHave('escortAssignment', function ($query) {
                $query->where('status', 'active');
            })
            ->get();

        return view('bus-escort-assignments.index', compact('routes', 'availableEscorts', 'unassignedRoutes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $busRoutes = BusRoute::with('bus')->get();
        return view('bus-escort-assignments.create', compact('busRoutes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'bus_route_id' => 'required|exists:bus_routes,id',
            'escort_regiment_no' => 'required|string|max:50',
            'escort_rank' => 'required|string|max:100',
            'escort_name' => 'required|string|max:200',
            'escort_contact_no' => 'required|string|max:20',
            'assigned_date' => 'required|date',
            'end_date' => 'nullable|date|after:assigned_date',
            'status' => 'required|in:active,inactive',
        ]);

        // Check if there's already an active assignment for this route
        if ($validatedData['status'] === 'active') {
            $existingAssignment = BusEscortAssignment::where('bus_route_id', $validatedData['bus_route_id'])
                ->where('status', 'active')
                ->first();

            if ($existingAssignment) {
                return back()->withErrors(['bus_route_id' => 'This bus route already has an active escort assignment.'])
                    ->withInput();
            }
        }

        // Check if escort exists by regiment number
        $escort = Escort::where('regiment_no', $request->escort_regiment_no)->first();

        if (!$escort) {
            // Create new escort if doesn't exist
            $escort = Escort::create([
                'regiment_no' => $request->escort_regiment_no,
                'rank' => $request->escort_rank,
                'name' => $request->escort_name,
                'contact_no' => $request->escort_contact_no,
            ]);
        } else {
            // Update existing escort information
            $escort->update([
                'rank' => $request->escort_rank,
                'name' => $request->escort_name,
                'contact_no' => $request->escort_contact_no,
            ]);
        }

        // Create assignment with escort_id instead of individual fields
        $assignmentData = [
            'bus_route_id' => $validatedData['bus_route_id'],
            'escort_id' => $escort->id,
            'assigned_date' => $validatedData['assigned_date'],
            'end_date' => $validatedData['end_date'],
            'status' => $validatedData['status'],
            'created_by' => Auth::user()->name ?? 'System'
        ];

        BusEscortAssignment::create($assignmentData);

        return redirect()->route('bus-escort-assignments.index')
            ->with('success', 'Bus escort assignment created successfully. Escort information has been ' . ($escort->wasRecentlyCreated ? 'created' : 'updated') . ' in the system.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BusEscortAssignment $bus_escort_assignment)
    {
        $bus_escort_assignment->load(['busRoute.bus']);
        $assignment = $bus_escort_assignment;
        return view('bus-escort-assignments.show', compact('assignment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BusEscortAssignment $bus_escort_assignment)
    {
        $busRoutes = BusRoute::with('bus')->get();
        $assignment = $bus_escort_assignment;
        return view('bus-escort-assignments.edit', compact('assignment', 'busRoutes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BusEscortAssignment $bus_escort_assignment)
    {
        $validatedData = $request->validate([
            'bus_route_id' => 'required|exists:bus_routes,id',
            'escort_regiment_no' => 'required|string|max:50',
            'escort_rank' => 'required|string|max:100',
            'escort_name' => 'required|string|max:200',
            'escort_contact_no' => 'required|string|max:20',
            'assigned_date' => 'required|date',
            'end_date' => 'nullable|date|after:assigned_date',
            'status' => 'required|in:active,inactive',
        ]);

        // Check if there's already an active assignment for this route (excluding current assignment)
        if ($validatedData['status'] === 'active') {
            $existingAssignment = BusEscortAssignment::where('bus_route_id', $validatedData['bus_route_id'])
                ->where('status', 'active')
                ->where('id', '!=', $bus_escort_assignment->id)
                ->first();

            if ($existingAssignment) {
                return back()->withErrors(['bus_route_id' => 'This bus route already has an active escort assignment.'])
                    ->withInput();
            }
        }

        // Update or create escort information
        $escort = Escort::where('regiment_no', $request->escort_regiment_no)->first();

        if (!$escort) {
            // Create new escort if doesn't exist
            $escort = Escort::create([
                'regiment_no' => $request->escort_regiment_no,
                'rank' => $request->escort_rank,
                'name' => $request->escort_name,
                'contact_no' => $request->escort_contact_no,
            ]);
        } else {
            // Update existing escort information
            $escort->update([
                'rank' => $request->escort_rank,
                'name' => $request->escort_name,
                'contact_no' => $request->escort_contact_no,
            ]);
        }

        // Update assignment with escort_id instead of individual fields
        $assignmentData = [
            'bus_route_id' => $validatedData['bus_route_id'],
            'escort_id' => $escort->id,
            'assigned_date' => $validatedData['assigned_date'],
            'end_date' => $validatedData['end_date'],
            'status' => $validatedData['status']
        ];

        $bus_escort_assignment->update($assignmentData);

        return redirect()->route('bus-escort-assignments.index')
            ->with('success', 'Bus escort assignment updated successfully. Escort information has been updated in the system.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BusEscortAssignment $bus_escort_assignment)
    {
        $bus_escort_assignment->delete();

        return redirect()->route('bus-escort-assignments.index')
            ->with('success', 'Bus escort assignment deleted successfully.');
    }

    /**
     * Assign an escort to a route
     */
    public function assign(Request $request)
    {
        $request->validate([
            'escort_id' => 'required|exists:escorts,id',
            'route_id' => 'required|exists:bus_routes,id',
            'assigned_date' => 'required|date',
            'end_date' => 'nullable|date|after:assigned_date',
        ]);

        $escort = Escort::findOrFail($request->escort_id);
        $route = BusRoute::findOrFail($request->route_id);

        // Check if escort is already assigned to an active route
        $existingEscortAssignment = BusEscortAssignment::where('escort_id', $request->escort_id)
            ->where('status', 'active')
            ->first();
        if ($existingEscortAssignment) {
            $existingRoute = BusRoute::find($existingEscortAssignment->bus_route_id);
            $escortName = ($escort->rank ?? 'N/A') . ' ' . ($escort->name ?? 'Unknown');
            $routeName = $existingRoute ? $existingRoute->name : 'Unknown Route';
            return response()->json([
                'success' => false,
                'message' => "Escort {$escortName} is already assigned to route {$routeName}."
            ]);
        }

        // Check if route already has an active escort
        $existingRouteAssignment = BusEscortAssignment::where('bus_route_id', $request->route_id)
            ->where('status', 'active')
            ->first();
        if ($existingRouteAssignment) {
            $existingEscort = Escort::find($existingRouteAssignment->escort_id);
            $existingEscortName = $existingEscort ? (($existingEscort->rank ?? 'N/A') . ' ' . ($existingEscort->name ?? 'Unknown')) : 'Unknown Escort';
            return response()->json([
                'success' => false,
                'message' => "Route {$route->name} already has escort {$existingEscortName} assigned."
            ]);
        }

        // Create the assignment
        BusEscortAssignment::create([
            'bus_route_id' => $request->route_id,
            'escort_id' => $request->escort_id,
            'assigned_date' => $request->assigned_date,
            'end_date' => $request->end_date,
            'status' => 'active',
            'created_by' => Auth::user()->name ?? 'System'
        ]);

        $escortName = ($escort->rank ?? 'N/A') . ' ' . ($escort->name ?? 'Unknown');
        return response()->json([
            'success' => true,
            'message' => "Escort {$escortName} has been successfully assigned to route {$route->name}."
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

    /**
     * Get escort details from Strength Management System API
     */
    public function getEscortDetails(Request $request)
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
                    $escortData = [
                        'rank' => $data['rank'] ?? '',
                        'name' => $data['name'] ?? '',
                        // Since contact_no is not directly available in the API response,
                        // we'll leave it empty for the user to fill in
                        'contact_no' => ''
                    ];

                    return response()->json([
                        'success' => true,
                        'data' => $escortData
                    ]);
                }

                return response()->json(['success' => false, 'message' => 'No data found for this regiment number'], 404);
            }

            return response()->json(['success' => false, 'message' => 'Failed to fetch data from Army API'], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching escort details: ' . $e->getMessage()
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
