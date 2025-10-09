<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PoliceStation;
use App\DataTables\PoliceStationDataTable;

class PoliceStationController extends Controller
{
    /**
     * Display a listing of the resorce.
     */
    public function index(PoliceStationDataTable $datatable)
    {
        return $datatable->render('police-station.index');
    }

    /**
     * show the form for creating a new resource.
     */
    public function create()
    {
        return view('police-station.create');
    }

    /**
     * store a newly created resource in storage
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50|unique:police_stations,name',  
        ]);

        PoliceStation::create($request->all());

        return redirect()->route('police-station.index')
              ->with('success','Police Station created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $police = PoliceStation::findOrfail($id);
        return view('police-station.show', compact('police'));
    }

    /**
     * show the form for editing the specified resource
     */
    public function edit(string $id)
    {
        $police = PoliceStation::findOrFail($id);
        return view('police-station.edit', compact('police'));
    }


    /**
     * update the specified resource
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|max:50|unique:police_stations,name,' .$id,
        ]);
        $police = PoliceStation::findOrFail($id);
        $police->update($request->all());
        return redirect()->route('police-station.index')
               ->with('success', 'Police Station updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $police = PoliceStation::findOrFail($id);
        $police->delete();
        return redirect()->route('police-station.index')
               ->with('success', 'Police Station deleted successfully.');
    }


}