<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IncidentType;
use App\DataTables\IncidentTypeDataTable;

class IncidentTypeController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(IncidentTypeDataTable $dataTable)
    {
        return $dataTable->render('incident-type.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('incident-type.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50|unique:incident_types,name',
        ]);

        IncidentType::create($request->all());

        return redirect()->route('incident-type.index')
            ->with('success', 'Incident Type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $incidentType = IncidentType::findOrFail($id);
        return view('incident-type.show', compact('incidentType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $incidentType = IncidentType::findOrFail($id);
        return view('incident-type.edit', compact('incidentType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|max:50|unique:incident_types,name,' . $id,
        ]);
        $incidentType = IncidentType::findOrFail($id);
        $incidentType->update($request->all());
        return redirect()->route('incident-type.index')
            ->with('success', 'Incident Type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $incidentType = IncidentType::findOrFail($id);
        $incidentType->delete();
        return redirect()->route('incident-type.index')
            ->with('success', 'Incident Type deleted successfully.');
    }
}
