<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\EstablishmentDataTable;
use App\Models\Establishment;
use Exception;
use Illuminate\Support\Facades\Log;

class EstablishmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(EstablishmentDataTable $dataTable)
    {
        return $dataTable->render('establishment.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('establishment.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255|unique:establishments,name',
        ]);

        Establishment::create([
            'name' => $request->name,
            'is_active' => true,
        ]);

        return redirect()->route('establishment.index')
            ->with('success', 'Establishment created successfully.');
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $establishment = Establishment::findOrFail($id);
        return view('establishment.show', compact('establishment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $establishment = Establishment::findOrFail($id);
        return view('establishment.edit', compact('establishment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $establishment = Establishment::findOrFail($id);

        $request->validate([
            'name' => 'required|max:255|unique:establishments,name,' . $id,
        ]);

        $establishment->update($request->all());

        return redirect()->route('establishment.index')
            ->with('success', 'Establishment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $establishment = Establishment::findOrFail($id);
        $establishment->delete();

        return redirect()->route('establishment.index')
            ->with('success', 'Establishment deleted successfully.');
    }
}
