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
    public function index(BusFillingStationAssignmentDataTable $dataTable)
    {
        return $dataTable->render('bus-filling-station-assignments.index');
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
