<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\BusDataTable;
use App\Models\Bus;
use App\Models\BusType;


class BusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(BusDataTable $dataTable)
    {
        return $dataTable->render('buses.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $busTypes = BusType::all();
        return view('buses.create', compact('busTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no' => 'required|unique:buses,no|max:20',
            'name' => 'required|max:50',
            'type_id' => 'required|integer|min:1',
            'no_of_seats' => 'required|integer|min:1',
            'total_capacity' => 'required|integer|min:1',
        ]);

        Bus::create($request->all());

        return redirect()->route('buses.index')
            ->with('success', 'Bus created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $bus = Bus::with('type')->findOrFail($id);
        return view('buses.show', compact('bus'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $bus = Bus::withCount(['routes', 'fillingStationAssignments'])->findOrFail($id);
        $busTypes = BusType::all();

        // Check if bus is being used elsewhere
        $isUsed = ($bus->routes_count > 0) || ($bus->filling_station_assignments_count > 0);

        // Build usage reasons for display
        $usageReasons = [];
        if ($bus->routes_count > 0) {
            $usageReasons[] = "assigned to {$bus->routes_count} route(s)";
        }
        if ($bus->filling_station_assignments_count > 0) {
            $usageReasons[] = "has {$bus->filling_station_assignments_count} filling station assignment(s)";
        }

        return view('buses.edit', compact('bus', 'busTypes', 'isUsed', 'usageReasons'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $bus = Bus::withCount(['routes', 'fillingStationAssignments'])->findOrFail($id);

        // Check if bus is being used elsewhere
        $isUsed = ($bus->routes_count > 0) || ($bus->filling_station_assignments_count > 0);

        // Validation rules
        $rules = [
            'name' => 'required|max:50',
            'type_id' => 'required|integer|min:1',
            'no_of_seats' => 'required|integer|min:1',
            'total_capacity' => 'required|integer|min:1',
        ];

        // Only validate bus number if it's not in use (allowing changes)
        if (!$isUsed) {
            $rules['no'] = 'required|max:20|unique:buses,no,' . $id;
        } else {
            // If bus is in use, ensure the submitted number matches the existing one
            $rules['no'] = 'required|max:20|in:' . $bus->no;
        }

        $request->validate($rules);

        // Prepare data for update
        $updateData = $request->only(['name', 'type_id', 'no_of_seats', 'total_capacity']);

        // Only update bus number if not in use
        if (!$isUsed) {
            $updateData['no'] = $request->no;
        }

        $bus->update($updateData);

        return redirect()->route('buses.index')
            ->with('success', 'Bus updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bus = Bus::findOrFail($id);
        $bus->delete();

        return redirect()->route('buses.index')
            ->with('success', 'Bus deleted successfully.');
    }
}
