<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GsDivision;
use App\DataTables\GsDivisionDataTable;

class GsDivisionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(GsDivisionDataTable $dataTable)
    {
        return $dataTable->render('gs-division.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('gs-division.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50|unique:gs_divisions,name',
        ]);

        GsDivision::create($request->only('name'));

        return redirect()->route('gs-devision.index')
            ->with('success', 'GS Division created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $gs_devision = GsDivision::findOrFail($id);
        return view('gs-division.show', compact('gs_devision'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $gs_devision = GsDivision::findOrFail($id);
        return view('gs-division.edit', compact('gs_devision'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|max:50|unique:gs_divisions,name,'.$id,
        ]);

        $gs_devision = GsDivision::findOrFail($id);
        $gs_devision->update($request->only('name'));

        return redirect()->route('gs-devision.index')
            ->with('success', 'GS Division updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $gs_devision = GsDivision::findOrFail($id);
        $gs_devision->delete();

        return redirect()->route('gs-devision.index')
            ->with('success', 'GS Division deleted successfully.');
    }
}
