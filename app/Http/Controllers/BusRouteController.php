<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\BusRouteDataTable;
use App\Models\BusRoute;
use App\Models\Bus;

class BusRouteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(BusRouteDataTable $dataTable)
    {
        return $dataTable->render('bus-routes.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $buses = Bus::all();
        return view('bus-routes.create', compact('buses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'bus_id' => 'required|exists:buses,id',
        ]);

        BusRoute::create($request->all());

        return redirect()->route('bus-routes.index')
            ->with('success', 'Bus route created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $busRoute = BusRoute::with('bus')->findOrFail($id);
        return view('bus-routes.show', compact('busRoute'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $busRoute = BusRoute::findOrFail($id);
        $buses = Bus::all();
        return view('bus-routes.edit', compact('busRoute', 'buses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $busRoute = BusRoute::findOrFail($id);

        $request->validate([
            'name' => 'required|max:100',
            'bus_id' => 'required|exists:buses,id',
        ]);

        $busRoute->update($request->all());

        return redirect()->route('bus-routes.index')
            ->with('success', 'Bus route updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $busRoute = BusRoute::findOrFail($id);
        $busRoute->delete();

        return redirect()->route('bus-routes.index')
            ->with('success', 'Bus route deleted successfully.');
    }
}
