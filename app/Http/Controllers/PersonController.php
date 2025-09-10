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
            'grama_seva_division' => 'required|max:100',
            'nearest_police_station' => 'required|max:100',
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
        $person = Person::findOrFail($id);
        return view('persons.edit', compact('person'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $person = Person::findOrFail($id);

        $request->validate([
            'regiment_no' => 'required|max:20|unique:persons,regiment_no,' . $id,
            'rank' => 'required|max:50',
            'name' => 'required|max:100',
            'unit' => 'required|max:100',
            'nic' => 'required|max:15',
            'army_id' => 'required|max:50',
            'permanent_address' => 'required',
            'telephone_no' => 'required|max:20',
            'grama_seva_division' => 'required|max:100',
            'nearest_police_station' => 'required|max:100',
        ]);

        $person->update($request->all());

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
                        'grama_seva_division' => $data['grama_seva_division'] ?? '',
                        'nearest_police_station' => $data['nearest_police_station'] ?? ''
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
