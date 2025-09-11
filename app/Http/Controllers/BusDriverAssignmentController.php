<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\DataTables\BusDriverAssignmentDataTable;
use App\Models\BusDriverAssignment;
use App\Models\BusRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class BusDriverAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(BusDriverAssignmentDataTable $dataTable)
    {
        return $dataTable->render('bus-driver-assignments.index');
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

        $validatedData['created_by'] = Auth::user()->name ?? 'System';

        BusDriverAssignment::create($validatedData);

        return redirect()->route('bus-driver-assignments.index')
            ->with('success', 'Bus driver assignment created successfully.');
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

        $bus_driver_assignment->update($validatedData);

        return redirect()->route('bus-driver-assignments.index')
            ->with('success', 'Bus driver assignment updated successfully.');
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
