<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\FillingStationDataTable;
use App\Models\FillingStation;

class FillingStationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(FillingStationDataTable $dataTable)
    {
        return $dataTable->render('filling-stations.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('filling-stations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        FillingStation::create($request->all());

        return redirect()->route('filling-stations.index')
            ->with('success', 'Filling station created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $fillingStation = FillingStation::findOrFail($id);
        return view('filling-stations.show', compact('fillingStation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $fillingStation = FillingStation::findOrFail($id);
        return view('filling-stations.edit', compact('fillingStation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $fillingStation = FillingStation::findOrFail($id);

        $request->validate([
            'name' => 'required|max:255',
        ]);

        $fillingStation->update($request->all());

        return redirect()->route('filling-stations.index')
            ->with('success', 'Filling station updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $fillingStation = FillingStation::findOrFail($id);
        $fillingStation->delete();

        return redirect()->route('filling-stations.index')
            ->with('success', 'Filling station deleted successfully.');
    }
}
