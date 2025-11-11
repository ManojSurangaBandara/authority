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
        return view('bus-routes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
        ]);

        BusRoute::create($request->only(['name']));

        return redirect()->route('bus-routes.index')
            ->with('success', 'Bus route created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $busRoute = BusRoute::with('bus.type')->findOrFail($id);
        return view('bus-routes.show', compact('busRoute'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $busRoute = BusRoute::findOrFail($id);
        return view('bus-routes.edit', compact('busRoute'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $busRoute = BusRoute::findOrFail($id);

        $request->validate([
            'name' => 'required|max:100',
        ]);

        $busRoute->update($request->only(['name']));

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

    /**
     * Get all bus routes for API
     */
    public function api()
    {
        try {
            $routes = BusRoute::select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $routes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch bus routes'
            ], 500);
        }
    }
}
