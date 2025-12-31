<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DestinationLocation;
use App\DataTables\DestinationLocationDataTable;
use App\Models\BusPassApplication;

class DestinationLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(DestinationLocationDataTable $dataTable)
    {
        return $dataTable->render('destination-locations.index');
    }

    /**
     * show the form for creating a new resource.
     */
    public function create()
    {
        return view('destination-locations.create');
    }

    /**
     * store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'destination_location' => 'required|max:50|unique:destination_locations,destination_location',
        ]);

        DestinationLocation::create($request->all());

        return redirect()->route('destination-locations.index')
            ->with('success', 'Entry created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $destinationLocation = DestinationLocation::findOrFail($id);
        return view('destination-locations.show', compact('destinationLocation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $destinationLocation = DestinationLocation::findOrFail($id);

        // Check if this destination location is being used in any applications
        $isUsed = BusPassApplication::where('destination_location_ahq', $destinationLocation->destination_location)->exists();

        if ($isUsed) {
            return redirect()->route('destination-locations.index')
                ->with('error', 'Cannot edit this destination location as it is currently being used in bus pass applications.');
        }

        return view('destination-locations.edit', compact('destinationLocation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'destination_location' => 'required|max:50|unique:destination_locations,destination_location,' . $id,
        ]);

        $destinationLocation = DestinationLocation::findOrFail($id);
        $destinationLocation->update($request->all());

        return redirect()->route('destination-locations.index')
            ->with('success', 'Entry updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $destinationLocation = DestinationLocation::findOrFail($id);

        // Check if this destination location is being used in any applications
        $isUsed = BusPassApplication::where('destination_location_ahq', $destinationLocation->destination_location)->exists();

        if ($isUsed) {
            return redirect()->route('destination-locations.index')
                ->with('error', 'Cannot delete this destination location as it is currently being used in bus pass applications.');
        }

        $destinationLocation->delete();

        return redirect()->route('destination-locations.index')
            ->with('success', 'Entry deleted successfully.');
    }

    /**
     * Get all destination locations for API
     */
    public function api()
    {
        try {
            $locations = DestinationLocation::select('id', 'destination_location as name')
                ->orderBy('destination_location')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $locations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch destination locations'
            ], 500);
        }
    }
}
