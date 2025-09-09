<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\EscortDataTable;
use App\Models\Escort;
use Illuminate\Support\Facades\Http;

class EscortController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(EscortDataTable $dataTable)
    {
        return $dataTable->render('escorts.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('escorts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'regiment_no' => 'required|unique:escorts,regiment_no|max:20',
            'rank' => 'required|max:50',
            'name' => 'required|max:100',
            'contact_no' => 'required|max:20',
        ]);

        Escort::create($request->all());

        return redirect()->route('escorts.index')
            ->with('success', 'Escort created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $escort = Escort::findOrFail($id);
        return view('escorts.show', compact('escort'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $escort = Escort::findOrFail($id);
        return view('escorts.edit', compact('escort'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $escort = Escort::findOrFail($id);

        $request->validate([
            'regiment_no' => 'required|max:20|unique:escorts,regiment_no,' . $id,
            'rank' => 'required|max:50',
            'name' => 'required|max:100',
            'contact_no' => 'required|max:20',
        ]);

        $escort->update($request->all());

        return redirect()->route('escorts.index')
            ->with('success', 'Escort updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $escort = Escort::findOrFail($id);
        $escort->delete();

        return redirect()->route('escorts.index')
            ->with('success', 'Escort deleted successfully.');
    }

    /**
     * Get escort details from API based on regiment number
     */
    public function getEscortDetails(Request $request)
    {
        $regimentNo = $request->input('regiment_no');

        if (empty($regimentNo)) {
            return response()->json(['success' => false, 'message' => 'Regiment number is required'], 400);
        }

        try {
            // Call the actual Army API endpoint
            $apiToken = '1189d8dde195a36a9c4a721a390a74e6';
            $apiUrl = "https://str.army.lk/api/get_person/?str-token={$apiToken}&service_no={$regimentNo}";

            $response = Http::get($apiUrl);

            if ($response->successful()) {
                // The response comes as a JSON array with brackets, we need to decode it
                $responseData = json_decode($response->body(), true);
                
                // Check if the API returned valid data
                if (is_array($responseData) && !empty($responseData)) {
                    // Extract the first record from the array
                    $data = $responseData[0];
                    
                    // Map API response fields to our application fields
                    $escortData = [
                        'rank' => $data['rank'] ?? '',
                        'name' => $data['name'] ?? '',
                        // Since contact_no is not directly available in the API response,
                        // we'll leave it empty for the user to fill in
                        'contact_no' => ''
                    ];
                    
                    return response()->json([
                        'success' => true,
                        'data' => $escortData
                    ]);
                }
                
                return response()->json(['success' => false, 'message' => 'No data found for this regiment number'], 404);
            }
            
            return response()->json(['success' => false, 'message' => 'Failed to fetch data from Army API'], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching escort details: ' . $e->getMessage()
            ], 500);
        }
    }
}
