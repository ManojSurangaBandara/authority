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
            'no' => 'required|unique:buses,no|max:20|regex:/^[A-Za-z]/',
            'name' => 'required|unique:buses,name|max:50',
            'type_id' => 'required|integer|min:1',
            'no_of_seats' => 'required|integer|min:1',
            'total_capacity' => 'required|integer|min:1',
        ], [
            'no.regex' => 'Bus number must start with a letter (A-Z or a-z).',
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
        $bus = Bus::withCount([
            'routes',
            'fillingStationAssignments' => function ($query) {
                $query->where('status', 'active');
            },
            'routeAssignments' => function ($query) {
                $query->where('status', 'active');
            }
        ])->findOrFail($id);

        // Prevent editing if bus is assigned to routes or has active assignments
        if ($bus->routes_count > 0 || $bus->route_assignments_count > 0) {
            return redirect()->route('buses.index')
                ->with('error', 'Cannot edit bus: This bus is assigned to ' . ($bus->routes_count + $bus->route_assignments_count) . ' route(s)/assignment(s). Please remove the bus from all routes before editing.');
        }

        $busTypes = BusType::all();

        // Check if bus is being used elsewhere (for active filling station assignments)
        $isUsed = $bus->filling_station_assignments_count > 0;

        // Build usage reasons for display
        $usageReasons = [];
        if ($bus->filling_station_assignments_count > 0) {
            $usageReasons[] = "has {$bus->filling_station_assignments_count} active filling station assignment(s)";
        }

        return view('buses.edit', compact('bus', 'busTypes', 'isUsed', 'usageReasons'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $bus = Bus::withCount([
            'routes',
            'fillingStationAssignments' => function ($query) {
                $query->where('status', 'active');
            },
            'routeAssignments' => function ($query) {
                $query->where('status', 'active');
            }
        ])->findOrFail($id);

        // Prevent updating if bus is assigned to routes or has active assignments
        if ($bus->routes_count > 0 || $bus->route_assignments_count > 0) {
            return redirect()->route('buses.index')
                ->with('error', 'Cannot update bus: This bus is assigned to ' . ($bus->routes_count + $bus->route_assignments_count) . ' route(s)/assignment(s). Please remove the bus from all routes before updating.');
        }

        // Check if bus is being used elsewhere (for active filling station assignments)
        $isUsed = $bus->filling_station_assignments_count > 0;

        // Validation rules
        $rules = [
            'name' => 'required|unique:buses,name,' . $id . '|max:50',
            'type_id' => 'required|integer|min:1',
            'no_of_seats' => 'required|integer|min:1',
            'total_capacity' => 'required|integer|min:1',
        ];

        // Only validate bus number if it's not in use (allowing changes)
        if (!$isUsed) {
            $rules['no'] = 'required|max:20|unique:buses,no,' . $id . '|regex:/^[A-Za-z]/';
        } else {
            // If bus is in use, ensure the submitted number matches the existing one
            $rules['no'] = 'required|max:20|in:' . $bus->no;
        }

        $request->validate($rules, [
            'no.regex' => 'Bus number must start with a letter (A-Z or a-z).',
        ]);

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
        $bus = Bus::withCount([
            'routes',
            'fillingStationAssignments' => function ($query) {
                $query->where('status', 'active');
            },
            'routeAssignments' => function ($query) {
                $query->where('status', 'active');
            }
        ])->findOrFail($id);

        // Prevent deletion if bus is assigned to routes or has active assignments
        if ($bus->routes_count > 0 || $bus->filling_station_assignments_count > 0 || $bus->route_assignments_count > 0) {
            $usageReasons = [];
            if ($bus->routes_count > 0) {
                $usageReasons[] = "assigned to {$bus->routes_count} route(s)";
            }
            if ($bus->route_assignments_count > 0) {
                $usageReasons[] = "has {$bus->route_assignments_count} active route assignment(s)";
            }
            if ($bus->filling_station_assignments_count > 0) {
                $usageReasons[] = "has {$bus->filling_station_assignments_count} active filling station assignment(s)";
            }

            return redirect()->route('buses.index')
                ->with('error', 'Cannot delete bus: This bus is ' . implode(' and ', $usageReasons) . '. Please remove all assignments before deleting.');
        }

        $bus->delete();

        return redirect()->route('buses.index')
            ->with('success', 'Bus deleted successfully.');
    }
}
