<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LivingInBuses;
use App\DataTables\LivingInBusesDataTable;
use App\Models\BusPassApplication;

class LivingInBusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(LivingInBusesDataTable $dataTable)
    {
        return $dataTable->render('living-in-buses.index');
    }

    /**
     * show the form for creating a new resource.
     */
    public function create()
    {
        return view('living-in-buses.create');
    }

    /**
     * store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50|unique:living_in_buses,name',
        ]);

        LivingInBuses::create($request->all());

        return redirect()->route('living-in-buses.index')
            ->with('success', 'Entry created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $livingInBus = LivingInBuses::findOrFail($id);
        return view('living-in-buses.show', compact('livingInBus'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $livingInBus = LivingInBuses::findOrFail($id);

        // Check if this living in bus is being used in any applications
        $isUsed = BusPassApplication::where('living_in_bus', $livingInBus->name)->exists();

        if ($isUsed) {
            return redirect()->route('living-in-buses.index')
                ->with('error', 'Cannot edit this living in bus as it is currently being used in bus pass applications.');
        }

        return view('living-in-buses.edit', compact('livingInBus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $livingInBus = LivingInBuses::findOrFail($id);

        $rules = [
            'name' => 'required|max:50|unique:living_in_buses,name,' . $id,
        ];

        $request->validate($rules);

        $livingInBus->update($request->all());

        return redirect()->route('living-in-buses.index')
            ->with('success', 'Entry updates successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $livingInBus = LivingInBuses::findOrFail($id);

        // Check if this living in bus is being used in any applications
        $isUsed = BusPassApplication::where('living_in_bus', $livingInBus->name)->exists();

        if ($isUsed) {
            return redirect()->route('living-in-buses.index')
                ->with('error', 'Cannot delete this living in bus as it is currently being used in bus pass applications.');
        }

        $livingInBus->delete();

        return redirect()->route('living-in-buses.index')
            ->with('success', 'Entry deleted successfully.');
    }

    /**
     * Get all living in buses for API
     */
    public function api()
    {
        try {
            $livingInBuses = LivingInBuses::select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $livingInBuses
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch living in buses'
            ], 500);
        }
    }
}
