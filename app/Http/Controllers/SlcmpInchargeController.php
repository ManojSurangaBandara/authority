<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\SlcmpInchargeDataTable;
use App\Models\SlcmpIncharge;
use Illuminate\Support\Facades\Http;

class SlcmpInchargeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SlcmpInchargeDataTable $dataTable)
    {
        return $dataTable->render('slcmp-incharges.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('slcmp-incharges.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'regiment_no' => 'required|unique:slcmp_incharges,regiment_no|max:20',
            'rank' => 'required|max:50',
            'name' => 'required|max:100',
            'contact_no' => 'required|max:20',
        ]);

        SlcmpIncharge::create($request->all());

        return redirect()->route('slcmp-incharges.index')
            ->with('success', 'SLCMP in charge created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $slcmpIncharge = SlcmpIncharge::findOrFail($id);
        return view('slcmp-incharges.show', compact('slcmpIncharge'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $slcmpIncharge = SlcmpIncharge::withCount('slcmpInchargeAssignments')->findOrFail($id);

        // Check if SLCMP incharge has active assignments
        $activeAssignmentsCount = $slcmpIncharge->slcmpInchargeAssignments()->where('status', 'active')->count();
        $isUsed = $activeAssignmentsCount > 0;

        return view('slcmp-incharges.edit', compact('slcmpIncharge', 'isUsed', 'activeAssignmentsCount'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $slcmpIncharge = SlcmpIncharge::findOrFail($id);

        // Check if SLCMP incharge has active assignments
        $activeAssignmentsCount = $slcmpIncharge->slcmpInchargeAssignments()->where('status', 'active')->count();
        $isUsed = $activeAssignmentsCount > 0;

        // Validation rules
        $rules = [
            'rank' => 'required|max:50',
            'name' => 'required|max:100',
            'contact_no' => 'required|max:20',
        ];

        // Only validate regiment_no if it's not in use (allowing changes)
        if (!$isUsed) {
            $rules['regiment_no'] = 'required|max:20|unique:slcmp_incharges,regiment_no,' . $id;
        } else {
            // If SLCMP incharge is in use, ensure the submitted regiment_no matches the existing one
            $rules['regiment_no'] = 'required|max:20|in:' . $slcmpIncharge->regiment_no;
        }

        $request->validate($rules);

        // Prepare data for update
        $updateData = $request->only(['rank', 'name', 'contact_no']);

        // Only update regiment_no if not in use
        if (!$isUsed) {
            $updateData['regiment_no'] = $request->regiment_no;
        }

        $slcmpIncharge->update($updateData);

        return redirect()->route('slcmp-incharges.index')
            ->with('success', 'SLCMP in charge updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $slcmpIncharge = SlcmpIncharge::findOrFail($id);
        $slcmpIncharge->delete();

        return redirect()->route('slcmp-incharges.index')
            ->with('success', 'SLCMP in charge deleted successfully.');
    }

    /**
     * Get SLCMP in charge details from API based on regiment number
     */
    public function getSlcmpInchargeDetails(Request $request)
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
                    $slcmpInchargeData = [
                        'rank' => $data['rank'] ?? '',
                        'name' => $data['name'] ?? '',
                        // Since contact_no is not directly available in the API response,
                        // we'll leave it empty for the user to fill in
                        'contact_no' => ''
                    ];

                    return response()->json([
                        'success' => true,
                        'data' => $slcmpInchargeData
                    ]);
                }

                return response()->json(['success' => false, 'message' => 'No data found for this regiment number'], 404);
            }

            return response()->json(['success' => false, 'message' => 'Failed to fetch data from Army API'], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching SLCMP in charge details: ' . $e->getMessage()
            ], 500);
        }
    }
}
