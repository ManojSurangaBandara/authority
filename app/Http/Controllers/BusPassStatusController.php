<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BusPassStatus;
use Illuminate\Http\Request;

class BusPassStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statuses = BusPassStatus::ordered()->get();
        return view('bus-pass-statuses.index', compact('statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('bus-pass-statuses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:bus_pass_statuses,code',
            'label' => 'required|string|max:100',
            'badge_color' => 'required|string|max:20',
            'description' => 'nullable|string',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        BusPassStatus::create($request->all());

        return redirect()->route('bus-pass-statuses.index')
            ->with('success', 'Bus pass status created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BusPassStatus $busPassStatus)
    {
        return view('bus-pass-statuses.show', compact('busPassStatus'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BusPassStatus $busPassStatus)
    {
        return view('bus-pass-statuses.edit', compact('busPassStatus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BusPassStatus $busPassStatus)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:bus_pass_statuses,code,' . $busPassStatus->id,
            'label' => 'required|string|max:100',
            'badge_color' => 'required|string|max:20',
            'description' => 'nullable|string',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $busPassStatus->update($request->all());

        return redirect()->route('bus-pass-statuses.index')
            ->with('success', 'Bus pass status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BusPassStatus $busPassStatus)
    {
        $busPassStatus->delete();

        return redirect()->route('bus-pass-statuses.index')
            ->with('success', 'Bus pass status deleted successfully.');
    }
}
