<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\BusDataTable;
use App\Models\Bus;

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
        return view('buses.create');
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
        $bus = Bus::findOrFail($id);
        return view('buses.show', compact('bus'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $bus = Bus::findOrFail($id);
        return view('buses.edit', compact('bus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $bus = Bus::findOrFail($id);

        $request->validate([
            'no' => 'required|max:20|unique:buses,no,' . $id,
            'name' => 'required|max:50',
            'type_id' => 'required|integer|min:1',
            'no_of_seats' => 'required|integer|min:1',
        ]);

        $bus->update($request->all());

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
