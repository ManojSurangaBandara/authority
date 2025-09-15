<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\BusPassApplicationDataTable;
use App\Models\BusPassApplication;
use App\Models\BusRoute;
use App\Models\Establishment;
use App\Models\Person;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BusPassApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(BusPassApplicationDataTable $dataTable)
    {
        return $dataTable->render('bus-pass-applications.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $busRoutes = BusRoute::all();
        $establishment = Establishment::orderBy('name')->get();

        return view('bus-pass-applications.create', compact('busRoutes','establishment'));
       
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validationRules = [
            'regiment_no' => 'required|string|max:20',
            'rank' => 'required|string|max:50',
            'name' => 'required|string|max:100',
            'unit' => 'required|string|max:100',
            'nic' => 'required|string|max:15',
            'army_id' => 'required|string|max:50',
            'permanent_address' => 'required|string',
            'telephone_no' => 'required|string|max:20',
            'grama_seva_division' => 'required|string|max:100',
            'nearest_police_station' => 'required|string|max:100',
            'branch_directorate' => 'required|string|max:100',
            'marital_status' => 'required|in:single,married',
            'approval_living_out' => 'required|in:yes,no',
            'obtain_sltb_season' => 'required|in:yes,no',
            'date_arrival_ahq' => 'required|date',
            'grama_niladari_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'person_image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'bus_pass_type' => 'required|in:daily_travel,weekend_monthly_travel',
            'rent_allowance_order' => $request->bus_pass_type === 'daily_travel' ? 'required|file|mimes:pdf,jpg,jpeg,png|max:2048' : 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'declaration_1' => 'required|in:yes',
            'declaration_2' => 'required|in:yes',
            
        ];

        // Add conditional validation for weekend/monthly travel
        if ($request->bus_pass_type === 'weekend_monthly_travel') {
            $validationRules['living_in_bus'] = 'required|string|max:100';
            $validationRules['destination_location_ahq'] = 'required|string|max:100';
            $validationRules['weekend_bus_name'] = 'required|string|max:100';
            $validationRules['weekend_destination'] = 'required|string|max:200';
        }

        $request->validate($validationRules);

        // Check if person exists by regiment number
        $person = Person::where('regiment_no', $request->regiment_no)->first();

        if (!$person) {
            // Create new person if doesn't exist
            $person = Person::create([
                'regiment_no' => $request->regiment_no,
                'rank' => $request->rank,
                'name' => $request->name,
                'unit' => $request->unit,
                'nic' => $request->nic,
                'army_id' => $request->army_id,
                'permanent_address' => $request->permanent_address,
                'telephone_no' => $request->telephone_no,
                'grama_seva_division' => $request->grama_seva_division,
                'nearest_police_station' => $request->nearest_police_station,
            ]);
        }

        // Handle file uploads
        $data = [];

        if ($request->hasFile('grama_niladari_certificate')) {
            $data['grama_niladari_certificate'] = $request->file('grama_niladari_certificate')->store('certificates', 'public');
        }

        if ($request->hasFile('person_image')) {
            $data['person_image'] = $request->file('person_image')->store('person_images', 'public');
        }

        if ($request->hasFile('rent_allowance_order')) {
            $data['rent_allowance_order'] = $request->file('rent_allowance_order')->store('rent_allowances', 'public');
        }

        // Prepare application data (excluding person fields)
        $data['person_id'] = $person->id;
        $data['branch_directorate'] = $request->branch_directorate;
        $data['marital_status'] = $request->marital_status;
        $data['approval_living_out'] = $request->approval_living_out;
        $data['obtain_sltb_season'] = $request->obtain_sltb_season;
        $data['date_arrival_ahq'] = $request->date_arrival_ahq;
        $data['bus_pass_type'] = $request->bus_pass_type;
        $data['declaration_1'] = $request->declaration_1;
        $data['declaration_2'] = $request->declaration_2;
        $data['created_by'] = Auth::user()->name ?? 'System';
        $data['status'] = 'pending_subject_clerk';

        // Add conditional fields for weekend/monthly travel
        if ($request->bus_pass_type === 'weekend_monthly_travel') {
            $data['living_in_bus'] = $request->living_in_bus;
            $data['destination_location_ahq'] = $request->destination_location_ahq;
            $data['weekend_bus_name'] = $request->weekend_bus_name;
            $data['weekend_destination'] = $request->weekend_destination;
        }

        // Add daily travel fields if present
        if ($request->has('requested_bus_name')) {
            $data['requested_bus_name'] = $request->requested_bus_name;
        }
        if ($request->has('destination_from_ahq')) {
            $data['destination_from_ahq'] = $request->destination_from_ahq;
        }

        BusPassApplication::create($data);

        return redirect()->route('bus-pass-applications.index')
            ->with('success', 'Bus pass application submitted successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BusPassApplication $bus_pass_application)
    {
        // Find the application by ID not to confused with peron table id

        return view('bus-pass-applications.show', compact('bus_pass_application'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BusPassApplication $bus_pass_application)
    {
        $busRoutes = BusRoute::all();

        return view('bus-pass-applications.edit', compact('bus_pass_application', 'busRoutes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BusPassApplication $bus_pass_application)
    {

        $validationRules = [
            'regiment_no' => 'required|string|max:20',
            'rank' => 'required|string|max:50',
            'name' => 'required|string|max:100',
            'unit' => 'required|string|max:100',
            'nic' => 'required|string|max:15',
            'army_id' => 'required|string|max:50',
            'permanent_address' => 'required|string',
            'telephone_no' => 'required|string|max:20',
            'grama_seva_division' => 'required|string|max:100',
            'nearest_police_station' => 'required|string|max:100',
            'branch_directorate' => 'required|string|max:100',
            'marital_status' => 'required|in:single,married',
            'approval_living_out' => 'required|in:yes,no',
            'obtain_sltb_season' => 'required|in:yes,no',
            'date_arrival_ahq' => 'required|date',
            'bus_pass_type' => 'required|in:daily_travel,weekend_monthly_travel',
            'rent_allowance_order' => $request->bus_pass_type === 'daily_travel' && !$bus_pass_application->rent_allowance_order ? 'required|file|mimes:pdf,jpg,jpeg,png|max:2048' : 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'declaration_1' => 'required|in:yes',
            'declaration_2' => 'required|in:yes',
        ];

        // Add conditional validation for weekend/monthly travel
        if ($request->bus_pass_type === 'weekend_monthly_travel') {
            $validationRules['living_in_bus'] = 'required|string|max:100';
            $validationRules['destination_location_ahq'] = 'required|string|max:100';
            $validationRules['weekend_bus_name'] = 'required|string|max:100';
            $validationRules['weekend_destination'] = 'required|string|max:200';
        }

        $request->validate($validationRules);

        // Update person data
        $bus_pass_application->person->update([
            'regiment_no' => $request->regiment_no,
            'rank' => $request->rank,
            'name' => $request->name,
            'unit' => $request->unit,
            'nic' => $request->nic,
            'army_id' => $request->army_id,
            'permanent_address' => $request->permanent_address,
            'telephone_no' => $request->telephone_no,
            'grama_seva_division' => $request->grama_seva_division,
            'nearest_police_station' => $request->nearest_police_station,
        ]);

        // Prepare application data (excluding person fields)
        $data = [];

        // Handle file uploads
        if ($request->hasFile('grama_niladari_certificate')) {
            if ($bus_pass_application->grama_niladari_certificate) {
                Storage::disk('public')->delete($bus_pass_application->grama_niladari_certificate);
            }
            $data['grama_niladari_certificate'] = $request->file('grama_niladari_certificate')->store('certificates', 'public');
        }

        if ($request->hasFile('person_image')) {
            if ($bus_pass_application->person_image) {
                Storage::disk('public')->delete($bus_pass_application->person_image);
            }
            $data['person_image'] = $request->file('person_image')->store('person_images', 'public');
        }

        if ($request->hasFile('rent_allowance_order')) {
            if ($bus_pass_application->rent_allowance_order) {
                Storage::disk('public')->delete($bus_pass_application->rent_allowance_order);
            }
            $data['rent_allowance_order'] = $request->file('rent_allowance_order')->store('rent_allowances', 'public');
        }

        // Add application-specific fields
        $data['branch_directorate'] = $request->branch_directorate;
        $data['marital_status'] = $request->marital_status;
        $data['approval_living_out'] = $request->approval_living_out;
        $data['obtain_sltb_season'] = $request->obtain_sltb_season;
        $data['date_arrival_ahq'] = $request->date_arrival_ahq;
        $data['bus_pass_type'] = $request->bus_pass_type;
        $data['declaration_1'] = $request->declaration_1;
        $data['declaration_2'] = $request->declaration_2;

        // Add conditional fields for weekend/monthly travel
        if ($request->bus_pass_type === 'weekend_monthly_travel') {
            $data['living_in_bus'] = $request->living_in_bus;
            $data['destination_location_ahq'] = $request->destination_location_ahq;
            $data['weekend_bus_name'] = $request->weekend_bus_name;
            $data['weekend_destination'] = $request->weekend_destination;
        }

        // Add daily travel fields if present
        if ($request->has('requested_bus_name')) {
            $data['requested_bus_name'] = $request->requested_bus_name;
        }
        if ($request->has('destination_from_ahq')) {
            $data['destination_from_ahq'] = $request->destination_from_ahq;
        }

        $bus_pass_application->update($data);

        return redirect()->route('bus-pass-applications.index')
            ->with('success', 'Bus pass application updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BusPassApplication $bus_pass_application)
    {
        // Delete associated files
        if ($bus_pass_application->grama_niladari_certificate) {
            Storage::disk('public')->delete($bus_pass_application->grama_niladari_certificate);
        }
        if ($bus_pass_application->person_image) {
            Storage::disk('public')->delete($bus_pass_application->person_image);
        }
        if ($bus_pass_application->rent_allowance_order) {
            Storage::disk('public')->delete($bus_pass_application->rent_allowance_order);
        }

        $bus_pass_application->delete();

        return redirect()->route('bus-pass-applications.index')
            ->with('success', 'Bus pass application deleted successfully.');
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
                $responseData = json_decode($response->body(), true);

                if (is_array($responseData) && !empty($responseData)) {
                    $data = $responseData[0];

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
