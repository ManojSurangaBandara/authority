<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\District;
use App\DataTables\DistrictDataTable;

class DistrictController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(DistrictDataTable $dataTable)
    {
        return $dataTable->render('district.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('district.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50|unique:districts,name',
        ]);

        District::create($request->all());

        return redirect()->route('district.index')
            ->with('success', 'District created successfully.');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $district = District::findOrFail($id);
        return view('district.show', compact('district'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $district = District::findOrFail($id);
        return view('district.edit', compact('district'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|max:50|unique:districts,name,'.$id,
        ]);
        $district = District::findOrFail($id);
        $district->update($request->all());
        return redirect()->route('district.index')
            ->with('success', 'District updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $district = District::findOrFail($id);
        $district->delete();
        return redirect()->route('district.index')
            ->with('success', 'District deleted successfully.');
    }


}

