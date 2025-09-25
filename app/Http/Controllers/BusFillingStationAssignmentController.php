<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\DataTables\BusFillingStationAssignmentDataTable;
use App\Models\BusFillingStationAssignment;
use App\Models\Bus;
use App\Models\FillingStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusFillingStationAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all buses with their assigned filling stations
        $buses = Bus::with(['type', 'fillingStationAssignment.fillingStation'])->get();

        // Get available filling stations (not currently assigned to active buses)
        $assignedFillingStationIds = BusFillingStationAssignment::where('status', 'active')
            ->whereNotNull('filling_station_id')
            ->pluck('filling_station_id')
            ->toArray();
        $availableFillingStations = FillingStation::whereNotIn('id', $assignedFillingStationIds)->orderBy('name')->get();

        // Get buses without active filling station assignments
        $unassignedBuses = Bus::with('type')
            ->whereDoesntHave('fillingStationAssignment', function ($query) {
                $query->where('status', 'active');
            })
            ->get();

        return view('bus-filling-station-assignments.index', compact('buses', 'availableFillingStations', 'unassignedBuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $buses = Bus::with('type')->get();
        $fillingStations = FillingStation::orderBy('name')->get();
        return view('bus-filling-station-assignments.create', compact('buses', 'fillingStations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'filling_station_id' => 'required|exists:filling_stations,id',
            'assigned_date' => 'required|date',
            'end_date' => 'nullable|date|after:assigned_date',
            'status' => 'required|in:active,inactive',
        ]);

        // Check if there's already an active assignment for this bus
        if ($validatedData['status'] === 'active') {
            $existingAssignment = BusFillingStationAssignment::where('bus_id', $validatedData['bus_id'])
                ->where('status', 'active')
                ->first();

            if ($existingAssignment) {
                return back()->withErrors(['bus_id' => 'This bus already has an active filling station assignment.'])
                    ->withInput();
            }
        }

        $validatedData['created_by'] = Auth::user()->name ?? 'System';

        BusFillingStationAssignment::create($validatedData);

        return redirect()->route('bus-filling-station-assignments.index')
            ->with('success', 'Bus filling station assignment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BusFillingStationAssignment $bus_filling_station_assignment)
    {
        $bus_filling_station_assignment->load(['bus.type', 'fillingStation']);
        $busFillingStationAssignment = $bus_filling_station_assignment;
        return view('bus-filling-station-assignments.show', compact('busFillingStationAssignment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BusFillingStationAssignment $bus_filling_station_assignment)
    {
        $buses = Bus::with('type')->get();
        $fillingStations = FillingStation::orderBy('name')->get();
        $bus_filling_station_assignment->load(['bus.type', 'fillingStation']);
        $busFillingStationAssignment = $bus_filling_station_assignment;
        return view('bus-filling-station-assignments.edit', compact('busFillingStationAssignment', 'buses', 'fillingStations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BusFillingStationAssignment $bus_filling_station_assignment)
    {
        $validatedData = $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'filling_station_id' => 'required|exists:filling_stations,id',
            'assigned_date' => 'required|date',
            'end_date' => 'nullable|date|after:assigned_date',
            'status' => 'required|in:active,inactive',
        ]);

        // Check if there's already an active assignment for this bus (excluding current assignment)
        if ($validatedData['status'] === 'active') {
            $existingAssignment = BusFillingStationAssignment::where('bus_id', $validatedData['bus_id'])
                ->where('status', 'active')
                ->where('id', '!=', $bus_filling_station_assignment->id)
                ->first();

            if ($existingAssignment) {
                return back()->withErrors(['bus_id' => 'This bus already has an active filling station assignment.'])
                    ->withInput();
            }
        }

        $bus_filling_station_assignment->update($validatedData);

        return redirect()->route('bus-filling-station-assignments.index')
            ->with('success', 'Bus filling station assignment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BusFillingStationAssignment $bus_filling_station_assignment)
    {
        $bus_filling_station_assignment->delete();

        return redirect()->route('bus-filling-station-assignments.index')
            ->with('success', 'Bus filling station assignment deleted successfully.');
    }

    /**
     * Assign a filling station to a bus
     */
    public function assign(Request $request)
    {
        $request->validate([
            'filling_station_id' => 'required|exists:filling_stations,id',
            'bus_id' => 'required|exists:buses,id',
            'assigned_date' => 'required|date',
            'end_date' => 'nullable|date|after:assigned_date',
        ]);

        $fillingStation = FillingStation::findOrFail($request->filling_station_id);
        $bus = Bus::findOrFail($request->bus_id);

        // Check if filling station is already assigned to an active bus
        $existingFillingStationAssignment = BusFillingStationAssignment::where('filling_station_id', $request->filling_station_id)
            ->where('status', 'active')
            ->first();
        if ($existingFillingStationAssignment) {
            $existingBus = Bus::find($existingFillingStationAssignment->bus_id);
            $existingBusName = $existingBus ? ($existingBus->name ?? 'Unknown') : 'Unknown';
            $existingBusNo = $existingBus ? ($existingBus->no ?? 'N/A') : 'N/A';
            return response()->json([
                'success' => false,
                'message' => "Filling station {$fillingStation->name} is already assigned to bus {$existingBusName} ({$existingBusNo})."
            ]);
        }

        // Check if bus already has an active filling station
        $existingBusAssignment = BusFillingStationAssignment::where('bus_id', $request->bus_id)
            ->where('status', 'active')
            ->first();
        if ($existingBusAssignment) {
            $existingFillingStation = FillingStation::find($existingBusAssignment->filling_station_id);
            $existingFillingStationName = $existingFillingStation ? ($existingFillingStation->name ?? 'Unknown') : 'Unknown';
            return response()->json([
                'success' => false,
                'message' => "Bus {$bus->name} ({$bus->no}) already has filling station {$existingFillingStationName} assigned."
            ]);
        }

        // Create the assignment
        BusFillingStationAssignment::create([
            'bus_id' => $request->bus_id,
            'filling_station_id' => $request->filling_station_id,
            'assigned_date' => $request->assigned_date,
            'end_date' => $request->end_date,
            'status' => 'active',
            'created_by' => Auth::user()->name ?? 'System'
        ]);

        return response()->json([
            'success' => true,
            'message' => "Filling station {$fillingStation->name} has been successfully assigned to bus {$bus->name} ({$bus->no})."
        ]);
    }

    /**
     * Unassign a filling station from a bus
     */
    public function unassign(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|exists:bus_filling_station_assignments,id',
        ]);

        $assignment = BusFillingStationAssignment::with(['fillingStation', 'bus'])->findOrFail($request->assignment_id);

        if ($assignment->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => "Assignment is not currently active."
            ]);
        }

        // Get filling station and bus info before updating (in case relationships become null)
        $fillingStationName = $assignment->fillingStation ? $assignment->fillingStation->name : 'Unknown Filling Station';
        $busName = $assignment->bus ? $assignment->bus->name . ' (' . $assignment->bus->no . ')' : 'Unknown Bus';

        // Check if there's already an inactive assignment for this bus
        $existingInactive = BusFillingStationAssignment::where('bus_id', $assignment->bus_id)
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
            'message' => "Filling station {$fillingStationName} has been successfully unassigned from bus {$busName}."
        ]);
    }

    /**
     * Get assignment data for AJAX requests
     */
    public function getAssignmentData()
    {
        $buses = Bus::with(['type', 'fillingStationAssignment.fillingStation'])->get();
        $availableFillingStations = FillingStation::whereDoesntHave('fillingStationAssignments', function ($query) {
            $query->where('status', 'active')->whereNotNull('filling_station_id');
        })->orderBy('name')->get();

        return response()->json([
            'buses' => $buses,
            'availableFillingStations' => $availableFillingStations
        ]);
    }

    /**
     * Get bus details when bus is selected
     */
    public function getBusDetails(Request $request)
    {
        $request->validate([
            'bus_id' => 'required|exists:buses,id'
        ]);

        $bus = Bus::with('type')->find($request->bus_id);

        if ($bus) {
            return response()->json([
                'success' => true,
                'data' => [
                    'bus_no' => $bus->no,
                    'bus_name' => $bus->name,
                    'bus_type' => $bus->type->name ?? 'N/A'
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Bus details not found.'
        ], 404);
    }
}
