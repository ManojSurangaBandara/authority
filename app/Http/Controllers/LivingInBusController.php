<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LivingInBuses;
use App\DataTables\LivingInBusesDataTable;

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
            'name' => 'required|max:50',
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
        return view('living-in-buses.edit', compact('livingInBus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $livingInBus = LivingInBuses::findOrFail($id);

        $rules = [
            'name' => 'required|max:50',
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
        $livingInBus->delete();

        return redirect()->route('living-in-buses.index')
            ->with('success', 'Entry deleted successfully.');
    }

}



        
     
     


   
