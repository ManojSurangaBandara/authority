<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\DataTables\SlcmpInchargeAssignmentDataTable;
use App\Models\SlcmpInchargeAssignment;
use App\Models\BusRoute;
use App\Models\SlcmpIncharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class SlcmpInchargeAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SlcmpInchargeAssignmentDataTable $dataTable)
    {
        return $dataTable->render('slcmp-incharge-assignments.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $busRoutes = BusRoute::with('bus.type')->get();
        return view('slcmp-incharge-assignments.create', compact('busRoutes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'bus_route_id' => 'required|exists:bus_routes,id',
            'slcmp_regiment_no' => 'required|string|max:50',
            'slcmp_rank' => 'required|string|max:100',
            'slcmp_name' => 'required|string|max:200',
            'slcmp_contact_no' => 'required|string|max:20',
            'assigned_date' => 'required|date',
            'end_date' => 'nullable|date|after:assigned_date',
            'status' => 'required|in:active,inactive',
        ]);

        // Check if there's already an active assignment for this route
        if ($validatedData['status'] === 'active') {
            $existingAssignment = SlcmpInchargeAssignment::where('bus_route_id', $validatedData['bus_route_id'])
                ->where('status', 'active')
                ->first();

            if ($existingAssignment) {
                return back()->withErrors(['bus_route_id' => 'This bus route already has an active SLCMP assignment.'])
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

        // Create assignment with slcmp_incharge_id instead of individual fields
        $assignmentData = [
            'bus_route_id' => $validatedData['bus_route_id'],
            'slcmp_incharge_id' => $slcmpIncharge->id,
            'assigned_date' => $validatedData['assigned_date'],
            'end_date' => $validatedData['end_date'],
            'status' => $validatedData['status'],
            'created_by' => Auth::user()->name ?? 'System'
        ];

        SlcmpInchargeAssignment::create($assignmentData);

        return redirect()->route('slcmp-incharge-assignments.index')
            ->with('success', 'SLCMP in-charge assignment created successfully. SLCMP in-charge information has been ' . ($slcmpIncharge->wasRecentlyCreated ? 'created' : 'updated') . ' in the system.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SlcmpInchargeAssignment $slcmp_incharge_assignment)
    {
        $slcmp_incharge_assignment->load(['busRoute.bus.type']);
        $slcmpInchargeAssignment = $slcmp_incharge_assignment;
        return view('slcmp-incharge-assignments.show', compact('slcmpInchargeAssignment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SlcmpInchargeAssignment $slcmp_incharge_assignment)
    {
        $busRoutes = BusRoute::with('bus.type')->get();
        $slcmp_incharge_assignment->load(['busRoute.bus.type']);
        $slcmpInchargeAssignment = $slcmp_incharge_assignment;
        return view('slcmp-incharge-assignments.edit', compact('slcmpInchargeAssignment', 'busRoutes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SlcmpInchargeAssignment $slcmp_incharge_assignment)
    {
        $validatedData = $request->validate([
            'bus_route_id' => 'required|exists:bus_routes,id',
            'slcmp_regiment_no' => 'required|string|max:50',
            'slcmp_rank' => 'required|string|max:100',
            'slcmp_name' => 'required|string|max:200',
            'slcmp_contact_no' => 'required|string|max:20',
            'assigned_date' => 'required|date',
            'end_date' => 'nullable|date|after:assigned_date',
            'status' => 'required|in:active,inactive',
        ]);

        // Check if there's already an active assignment for this route (excluding current assignment)
        if ($validatedData['status'] === 'active') {
            $existingAssignment = SlcmpInchargeAssignment::where('bus_route_id', $validatedData['bus_route_id'])
                ->where('status', 'active')
                ->where('id', '!=', $slcmp_incharge_assignment->id)
                ->first();

            if ($existingAssignment) {
                return back()->withErrors(['bus_route_id' => 'This bus route already has an active SLCMP assignment.'])
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

        // Update assignment with slcmp_incharge_id instead of individual fields
        $assignmentData = [
            'bus_route_id' => $validatedData['bus_route_id'],
            'slcmp_incharge_id' => $slcmpIncharge->id,
            'assigned_date' => $validatedData['assigned_date'],
            'end_date' => $validatedData['end_date'],
            'status' => $validatedData['status']
        ];

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
            'bus_route_id' => 'required|exists:bus_routes,id'
        ]);

        $busRoute = BusRoute::with('bus.type')->find($request->bus_route_id);

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
