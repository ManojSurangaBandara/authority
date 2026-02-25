<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\DriverDataTable;
use App\Models\Driver;
use Illuminate\Support\Facades\Http;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(DriverDataTable $dataTable)
    {
        return $dataTable->render('drivers.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('drivers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate input depending on driver type
        $type = $request->input('driver_type');

        // normalize NIC for civil drivers only
        if ($type === 'Civil' && $request->filled('nic')) {
            $clean = preg_replace('/\s+/', '', $request->nic);
            $request->merge(['nic' => strtoupper($clean)]);
        }

        $rules = [
            'driver_type' => 'required|in:Army,Civil',
            'name' => 'required|max:100',
            'contact_no' => 'required|max:20',
        ];

        if ($type === 'Army') {
            $rules['regiment_no'] = 'required|unique:drivers,regiment_no|max:20';
            $rules['rank'] = 'required|max:50';
        } else {
            // Civil driver
            $rules['nic'] = 'required|unique:drivers,nic|max:20';
            // rank and regiment_no are not required for civil
        }

        $request->validate($rules);

        // Prepare data array for creation; ensure fields not relevant to the type are null
        $data = [
            'driver_type' => $type,
            'name' => $request->name,
            'contact_no' => $request->contact_no,
        ];

        if ($type === 'Army') {
            $data['regiment_no'] = $request->regiment_no;
            $data['rank'] = $request->rank;
            $data['nic'] = null;
        } else {
            $data['nic'] = $request->nic;
            $data['regiment_no'] = null;
            $data['rank'] = null;
        }

        Driver::create($data);

        return redirect()->route('drivers.index')
            ->with('success', 'Driver created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $driver = Driver::findOrFail($id);
        return view('drivers.show', compact('driver'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $driver = Driver::withCount('driverAssignments')->findOrFail($id);

        // Check if driver has active assignments
        $activeAssignmentsCount = $driver->driverAssignments()->where('status', 'active')->count();
        $isUsed = $activeAssignmentsCount > 0;

        return view('drivers.edit', compact('driver', 'isUsed', 'activeAssignmentsCount'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $driver = Driver::findOrFail($id);

        // Check if driver has active assignments
        $activeAssignmentsCount = $driver->driverAssignments()->where('status', 'active')->count();
        $isUsed = $activeAssignmentsCount > 0;

        // Determine driver type; if not supplied fallback to existing
        $type = $request->input('driver_type', $driver->driver_type);
        // normalize NIC for civil drivers before validation
        if ($type === 'Civil' && $request->filled('nic')) {
            $clean = preg_replace('/\s+/', '', $request->nic);
            $request->merge(['nic' => strtoupper($clean)]);
        }

        // Basic rules
        $rules = [
            'driver_type' => 'required|in:Army,Civil',
            'name' => 'required|max:100',
            'contact_no' => 'required|max:20',
        ];

        if ($type === 'Army') {
            // regiment_no rules similar to store
            if (!$isUsed) {
                $rules['regiment_no'] = 'required|max:20|unique:drivers,regiment_no,' . $id;
            } else {
                $rules['regiment_no'] = 'required|max:20|in:' . $driver->regiment_no;
            }
            $rules['rank'] = 'required|max:50';
        } else {
            // Civil
            if (!$isUsed) {
                $rules['nic'] = 'required|max:20|unique:drivers,nic,' . $id;
            } else {
                $rules['nic'] = 'required|max:20|in:' . $driver->nic;
            }
            // rank and regiment_no not required
        }

        $request->validate($rules);

        // Build update array
        $updateData = [
            'driver_type' => $type,
            'name' => $request->name,
            'contact_no' => $request->contact_no,
        ];

        if ($type === 'Army') {
            $updateData['rank'] = $request->rank;
            if (!$isUsed) {
                $updateData['regiment_no'] = $request->regiment_no;
            }
            // clear civil fields
            $updateData['nic'] = null;
        } else {
            $updateData['nic'] = $request->nic;
            // clear army-specific fields when switching type or updating civil
            $updateData['regiment_no'] = null;
            $updateData['rank'] = null;
        }

        $driver->update($updateData);

        return redirect()->route('drivers.index')
            ->with('success', 'Driver updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $driver = Driver::findOrFail($id);
        $driver->delete();

        return redirect()->route('drivers.index')
            ->with('success', 'Driver deleted successfully.');
    }

    /**
     * Get driver details from API based on regiment number
     */
    public function getDriverDetails(Request $request)
    {
        // only valid for army drivers
        $type = $request->input('driver_type');
        if ($type !== 'Army') {
            return response()->json(['success' => false, 'message' => 'Invalid driver type'], 400);
        }

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
                    $driverData = [
                        'rank' => $data['rank'] ?? '',
                        'name' => $data['name'] ?? '',
                        // Since contact_no is not directly available in the API response,
                        // we'll leave it empty for the user to fill in
                        'contact_no' => ''
                    ];

                    return response()->json([
                        'success' => true,
                        'data' => $driverData
                    ]);
                }

                return response()->json(['success' => false, 'message' => 'No data found for this regiment number'], 404);
            }

            return response()->json(['success' => false, 'message' => 'Failed to fetch data from Army API'], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching driver details: ' . $e->getMessage()
            ], 500);
        }
    }
}
