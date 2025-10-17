<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\PersonDataTable;
use App\Models\Person;
use Illuminate\Support\Facades\Http;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PersonDataTable $dataTable)
    {
        return $dataTable->render('persons.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('persons.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'regiment_no' => 'required|unique:persons,regiment_no|max:20',
            'rank' => 'required|max:50',
            'name' => 'required|max:100',
            'unit' => 'required|max:100',
            'nic' => 'required|max:15',
            'army_id' => 'required|max:50',
            'permanent_address' => 'required',
            'telephone_no' => 'required|max:20',
            'province_id' => 'required|exists:provinces,id',
            'district_id' => 'required|exists:districts,id',
            'gs_division_id' => 'required|exists:gs_divisions,id',
            'police_station_id' => 'required|exists:police_stations,id',
        ]);

        Person::create($request->all());

        return redirect()->route('persons.index')
            ->with('success', 'Person created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $person = Person::findOrFail($id);
        return view('persons.show', compact('person'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $person = Person::withCount('busPassApplications')->findOrFail($id);

        // Check if person has bus pass applications
        $busPassApplicationsCount = $person->bus_pass_applications_count ?? 0;
        $isUsed = $busPassApplicationsCount > 0;

        return view('persons.edit', compact('person', 'isUsed', 'busPassApplicationsCount'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $person = Person::findOrFail($id);

        // Check if person has bus pass applications
        $busPassApplicationsCount = $person->busPassApplications()->count();
        $isUsed = $busPassApplicationsCount > 0;

        // Validation rules
        $rules = [
            'rank' => 'required|max:50',
            'name' => 'required|max:100',
            'unit' => 'required|max:100',
            'nic' => 'required|max:15',
            'army_id' => 'required|max:50',
            'permanent_address' => 'required',
            'telephone_no' => 'required|max:20',
            'province_id' => 'required|exists:provinces,id',
            'district_id' => 'required|exists:districts,id',
            'gs_division_id' => 'required|exists:gs_divisions,id',
            'police_station_id' => 'required|exists:police_stations,id',
        ];

        // Only validate regiment_no if it's not in use (allowing changes)
        if (!$isUsed) {
            $rules['regiment_no'] = 'required|max:20|unique:persons,regiment_no,' . $id;
        } else {
            // If person is in use, ensure the submitted regiment_no matches the existing one
            $rules['regiment_no'] = 'required|max:20|in:' . $person->regiment_no;
        }

        $request->validate($rules);

        // Prepare data for update
        $updateData = $request->only(['rank', 'name', 'unit', 'nic', 'army_id', 'permanent_address', 'telephone_no', 'province_id', 'district_id', 'gs_division_id', 'police_station_id']);

        // Only update regiment_no if not in use
        if (!$isUsed) {
            $updateData['regiment_no'] = $request->regiment_no;
        }

        $person->update($updateData);

        return redirect()->route('persons.index')
            ->with('success', 'Person updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $person = Person::findOrFail($id);
        $person->delete();

        return redirect()->route('persons.index')
            ->with('success', 'Person deleted successfully.');
    }

    /**
     * Get person details from API based on regiment number
     */
    public function getPersonDetails(Request $request)
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
                    $personData = [
                        'rank' => $data['rank'] ?? '',
                        'name' => $data['name'] ?? '',
                        'unit' => $data['unit'] ?? '',
                        'nic' => $data['nic'] ?? '',
                        'army_id' => $data['army_no'] ?? '',
                        'permanent_address' => $data['permanent_address'] ?? '',
                        'telephone_no' => $data['telephone_no'] ?? '',
                        'gs_division_id' => $data['gs_division_id'] ?? null,
                        'police_station_id' => $data['police_station_id'] ?? null
                    ];

                    return response()->json([
                        'success' => true,
                        'data' => $personData
                    ]);
                }

                return response()->json(['success' => false, 'message' => 'No data found for this regiment number'], 404);
            }

            return response()->json(['success' => false, 'message' => 'Failed to fetch data from Army API'], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching person details: ' . $e->getMessage()
            ], 500);
        }
    }
}
