<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\BusPassApplicationDataTable;
use App\Models\BusPassApplication;
use App\Models\BusRoute;
use App\Models\Establishment;
use App\Models\Person;
use App\Models\Province;
use App\Models\District;
use App\Models\GsDivision;
use App\Models\PoliceStation;
use App\Models\Rank;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        $provinces = Province::orderBy('name')->get();
        $districts = District::orderBy('name')->get();
        $gsDivisions = GsDivision::orderBy('name')->get();
        $policeStations = PoliceStation::orderBy('name')->get();
        $ranks = Rank::orderBy('id')->get();

        return view('bus-pass-applications.create', compact('busRoutes', 'establishment', 'provinces', 'districts', 'gsDivisions', 'policeStations', 'ranks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validationRules = [
            'regiment_no' => 'required|string|max:20',
            'rank_id' => 'required|exists:ranks,id',
            'name' => 'required|string|max:100',
            'unit' => 'required|string|max:100',
            'nic' => 'required|string|max:15',
            'army_id' => 'required|string|max:50',
            'permanent_address' => 'required|string',
            'telephone_no' => 'required|string|max:20',
            'province_id' => 'required|exists:provinces,id',
            'district_id' => 'required|exists:districts,id',
            'gs_division_id' => 'required|exists:gs_divisions,id',
            'police_station_id' => 'required|exists:police_stations,id',
            'marital_status' => 'required|in:single,married',
            'approval_living_out' => 'required|in:yes,no',
            'obtain_sltb_season' => 'required|in:yes,no',
            'date_arrival_ahq' => 'required|date',
            'grama_niladari_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'person_image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'bus_pass_type' => 'required|in:daily_travel,weekend_monthly_travel,living_in_only,weekend_only,unmarried_daily_travel',
            'rent_allowance_order' => ($request->marital_status === 'married' && $request->bus_pass_type !== 'living_in_only' && $request->bus_pass_type !== 'unmarried_daily_travel') ? 'required|file|mimes:pdf,jpg,jpeg,png|max:2048' : 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'permission_letter' => ($request->bus_pass_type === 'unmarried_daily_travel') ? 'required|file|mimes:pdf,jpg,jpeg,png|max:2048' : 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'declaration_1' => 'required|in:yes',
            'declaration_2' => 'required|in:yes',
        ];

        // Only require establishment_id for non-branch users
        $user = Auth::user();
        $branchRoles = ['Bus Pass Subject Clerk (Branch)', 'Staff Officer (Branch)', 'Director (Branch)'];
        if (!$user->hasAnyRole($branchRoles)) {
            $validationRules['establishment_id'] = 'required|exists:establishments,id';
        }

        // Add conditional validation based on bus pass type
        if ($request->bus_pass_type === 'daily_travel' || $request->bus_pass_type === 'unmarried_daily_travel') {
            $validationRules['requested_bus_name'] = 'required|string|max:100';
            $validationRules['destination_from_ahq'] = 'required|string|max:200';
        } elseif ($request->bus_pass_type === 'weekend_monthly_travel') {
            $validationRules['living_in_bus'] = 'required|string|max:100';
            $validationRules['destination_location_ahq'] = 'required|string|max:100';
            $validationRules['weekend_bus_name'] = 'required|string|max:100';
            $validationRules['weekend_destination'] = 'required|string|max:200';
        } elseif ($request->bus_pass_type === 'living_in_only') {
            $validationRules['living_in_bus'] = 'required|string|max:100';
            $validationRules['destination_location_ahq'] = 'required|exists:destination_locations,id';
        } elseif ($request->bus_pass_type === 'weekend_only') {
            $validationRules['weekend_bus_name'] = 'required|string|max:100';
            $validationRules['weekend_destination'] = 'required|string|max:200';
        }

        $request->validate($validationRules);

        // Additional validation: Single personnel validation
        if ($request->marital_status === 'single') {
            // Single personnel can select "Living in Bus only" or "Unmarried Daily Travel" (if approval for living out is yes)
            $allowedTypes = ['living_in_only'];
            if ($request->approval_living_out === 'yes') {
                $allowedTypes[] = 'unmarried_daily_travel';
            }

            if (!in_array($request->bus_pass_type, $allowedTypes)) {
                $errorMessage = $request->approval_living_out === 'yes'
                    ? 'Single personnel can only select "Living in Bus only" or "Unmarried Daily Travel" bus pass types.'
                    : 'Single personnel can only select "Living in Bus only" bus pass type.';

                return redirect()->back()
                    ->withErrors(['bus_pass_type' => $errorMessage])
                    ->withInput();
            }
        }

        // Debug: Log successful validation
        Log::info('Validation passed for bus pass application', [
            'regiment_no' => $request->regiment_no,
            'establishment_id' => $request->establishment_id
        ]);

        // Check if person exists by regiment number
        $person = Person::where('regiment_no', $request->regiment_no)->first();

        if (!$person) {
            // Create new person if doesn't exist
            $person = Person::create([
                'regiment_no' => $request->regiment_no,
                'rank_id' => $request->rank_id,
                'name' => $request->name,
                'unit' => $request->unit,
                'nic' => $request->nic,
                'army_id' => $request->army_id,
                'permanent_address' => $request->permanent_address,
                'telephone_no' => $request->telephone_no,
                'province_id' => $request->province_id,
                'district_id' => $request->district_id,
                'gs_division_id' => $request->gs_division_id,
                'police_station_id' => $request->police_station_id,
            ]);
        } else {
            // Update existing person data
            $person->update([
                'rank_id' => $request->rank_id,
                'name' => $request->name,
                'unit' => $request->unit,
                'nic' => $request->nic,
                'army_id' => $request->army_id,
                'permanent_address' => $request->permanent_address,
                'telephone_no' => $request->telephone_no,
                'province_id' => $request->province_id,
                'district_id' => $request->district_id,
                'gs_division_id' => $request->gs_division_id,
                'police_station_id' => $request->police_station_id,
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

        if ($request->hasFile('permission_letter')) {
            $data['permission_letter'] = $request->file('permission_letter')->store('permission_letters', 'public');
        }

        // Prepare application data (excluding person fields)
        $data['person_id'] = $person->id;

        // For branch users, automatically use their establishment
        $user = Auth::user();
        $branchRoles = ['Bus Pass Subject Clerk (Branch)', 'Staff Officer (Branch)', 'Director (Branch)'];
        if ($user->hasAnyRole($branchRoles)) {
            $data['establishment_id'] = $user->establishment_id;
        } else {
            $data['establishment_id'] = $request->establishment_id;
        }

        // Set branch_directorate to establishment name for now (since form doesn't have this field)
        $establishment = Establishment::find($data['establishment_id']);
        $data['branch_directorate'] = $establishment ? $establishment->name : 'Unknown';
        $data['marital_status'] = $request->marital_status;
        $data['approval_living_out'] = $request->approval_living_out;
        $data['obtain_sltb_season'] = $request->obtain_sltb_season;
        $data['date_arrival_ahq'] = $request->date_arrival_ahq;
        $data['bus_pass_type'] = $request->bus_pass_type;
        $data['declaration_1'] = $request->declaration_1;
        $data['declaration_2'] = $request->declaration_2;
        $data['created_by'] = Auth::user()->name ?? 'System';
        $data['status'] = 'pending_subject_clerk';

        // Add conditional fields based on bus pass type
        if ($request->bus_pass_type === 'daily_travel' || $request->bus_pass_type === 'unmarried_daily_travel') {
            $data['requested_bus_name'] = $request->requested_bus_name;
            $data['destination_from_ahq'] = $request->destination_from_ahq;
        } elseif ($request->bus_pass_type === 'weekend_monthly_travel') {
            $data['living_in_bus'] = $request->living_in_bus;
            $data['destination_location_ahq'] = $request->destination_location_ahq;
            $data['weekend_bus_name'] = $request->weekend_bus_name;
            $data['weekend_destination'] = $request->weekend_destination;
        } elseif ($request->bus_pass_type === 'living_in_only') {
            $data['living_in_bus'] = $request->living_in_bus;
            $data['destination_location_ahq'] = $request->destination_location_ahq;
        } elseif ($request->bus_pass_type === 'weekend_only') {
            $data['weekend_bus_name'] = $request->weekend_bus_name;
            $data['weekend_destination'] = $request->weekend_destination;
        }

        try {
            BusPassApplication::create($data);
            Log::info('Bus pass application created successfully', ['person_id' => $person->id]);

            return redirect()->route('bus-pass-applications.index')
                ->with('success', 'Bus pass application submitted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create bus pass application', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create application: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BusPassApplication $bus_pass_application)
    {
        // Load all person relationships including rank and police station
        $bus_pass_application->load('destinationLocation', 'person.rank', 'person.province', 'person.district', 'person.policeStation');

        return view('bus-pass-applications.show', compact('bus_pass_application'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BusPassApplication $bus_pass_application)
    {
        $busRoutes = BusRoute::all();
        $establishment = Establishment::orderBy('name')->get();
        $provinces = Province::orderBy('name')->get();
        $districts = District::orderBy('name')->get();
        $gsDivisions = GsDivision::orderBy('name')->get();
        $policeStations = PoliceStation::orderBy('name')->get();
        $ranks = Rank::orderBy('id')->get();

        // Load the destination location relationship if needed
        $bus_pass_application->load('destinationLocation');

        return view('bus-pass-applications.edit', compact('bus_pass_application', 'busRoutes', 'establishment', 'provinces', 'districts', 'gsDivisions', 'policeStations', 'ranks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BusPassApplication $bus_pass_application)
    {

        $validationRules = [
            'regiment_no' => 'required|string|max:20',
            'rank_id' => 'required|exists:ranks,id',
            'name' => 'required|string|max:100',
            'unit' => 'required|string|max:100',
            'nic' => 'required|string|max:15',
            'army_id' => 'required|string|max:50',
            'permanent_address' => 'required|string',
            'telephone_no' => 'required|string|max:20',
            'province_id' => 'required|exists:provinces,id',
            'district_id' => 'required|exists:districts,id',
            'gs_division_id' => 'required|exists:gs_divisions,id',
            'police_station_id' => 'required|exists:police_stations,id',
            'marital_status' => 'required|in:single,married',
            'approval_living_out' => 'required|in:yes,no',
            'obtain_sltb_season' => 'required|in:yes,no',
            'date_arrival_ahq' => 'required|date',
            'bus_pass_type' => 'required|in:daily_travel,weekend_monthly_travel,living_in_only,weekend_only,unmarried_daily_travel',
            'rent_allowance_order' => ($request->marital_status === 'married' && $request->bus_pass_type !== 'living_in_only' && $request->bus_pass_type !== 'unmarried_daily_travel' && !$bus_pass_application->rent_allowance_order) ? 'required|file|mimes:pdf,jpg,jpeg,png|max:2048' : 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'permission_letter' => ($request->bus_pass_type === 'unmarried_daily_travel' && !$bus_pass_application->permission_letter) ? 'required|file|mimes:pdf,jpg,jpeg,png|max:2048' : 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'declaration_1' => 'required|in:yes',
            'declaration_2' => 'required|in:yes',
        ];

        // Only require establishment_id for non-branch users
        $user = Auth::user();
        $branchRoles = ['Bus Pass Subject Clerk (Branch)', 'Staff Officer (Branch)', 'Director (Branch)'];
        if (!$user->hasAnyRole($branchRoles)) {
            $validationRules['establishment_id'] = 'required|exists:establishments,id';
        }

        // Add conditional validation based on bus pass type
        if ($request->bus_pass_type === 'daily_travel' || $request->bus_pass_type === 'unmarried_daily_travel') {
            $validationRules['requested_bus_name'] = 'required|string|max:100';
            $validationRules['destination_from_ahq'] = 'required|string|max:200';
        } elseif ($request->bus_pass_type === 'weekend_monthly_travel') {
            $validationRules['living_in_bus'] = 'required|string|max:100';
            $validationRules['destination_location_ahq'] = 'required|string|max:100';
            $validationRules['weekend_bus_name'] = 'required|string|max:100';
            $validationRules['weekend_destination'] = 'required|string|max:200';
        } elseif ($request->bus_pass_type === 'living_in_only') {
            $validationRules['living_in_bus'] = 'required|string|max:100';
            $validationRules['destination_location_ahq'] = 'required|string|max:100';
        } elseif ($request->bus_pass_type === 'weekend_only') {
            $validationRules['weekend_bus_name'] = 'required|string|max:100';
            $validationRules['weekend_destination'] = 'required|string|max:200';
        }

        $request->validate($validationRules);

        // Additional validation: Single personnel validation
        if ($request->marital_status === 'single') {
            // Single personnel can select "Living in Bus only" or "Unmarried Daily Travel" (if approval for living out is yes)
            $allowedTypes = ['living_in_only'];
            if ($request->approval_living_out === 'yes') {
                $allowedTypes[] = 'unmarried_daily_travel';
            }

            if (!in_array($request->bus_pass_type, $allowedTypes)) {
                $errorMessage = $request->approval_living_out === 'yes'
                    ? 'Single personnel can only select "Living in Bus only" or "Unmarried Daily Travel" bus pass types.'
                    : 'Single personnel can only select "Living in Bus only" bus pass type.';

                return redirect()->back()
                    ->withErrors(['bus_pass_type' => $errorMessage])
                    ->withInput();
            }
        }

        // Debug: Log successful validation
        Log::info('Update validation passed for bus pass application', [
            'id' => $bus_pass_application->id,
            'regiment_no' => $request->regiment_no,
            'establishment_id' => $request->establishment_id
        ]);

        // Update person data
        $bus_pass_application->person->update([
            'regiment_no' => $request->regiment_no,
            'rank_id' => $request->rank_id,
            'name' => $request->name,
            'unit' => $request->unit,
            'nic' => $request->nic,
            'army_id' => $request->army_id,
            'permanent_address' => $request->permanent_address,
            'telephone_no' => $request->telephone_no,
            'province_id' => $request->province_id,
            'district_id' => $request->district_id,
            'gs_division_id' => $request->gs_division_id,
            'police_station_id' => $request->police_station_id,
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

        if ($request->hasFile('permission_letter')) {
            if ($bus_pass_application->permission_letter) {
                Storage::disk('public')->delete($bus_pass_application->permission_letter);
            }
            $data['permission_letter'] = $request->file('permission_letter')->store('permission_letters', 'public');
        }

        // Add application-specific fields
        // Set establishment_id based on user role
        if ($user->hasAnyRole($branchRoles)) {
            $data['establishment_id'] = $user->establishment_id;
        } else {
            $data['establishment_id'] = $request->establishment_id;
        }

        // Set branch_directorate to establishment name for now (since form doesn't have this field)
        $establishment = Establishment::find($data['establishment_id']);
        $data['branch_directorate'] = $establishment ? $establishment->name : 'Unknown';
        $data['marital_status'] = $request->marital_status;
        $data['approval_living_out'] = $request->approval_living_out;
        $data['obtain_sltb_season'] = $request->obtain_sltb_season;
        $data['date_arrival_ahq'] = $request->date_arrival_ahq;
        $data['bus_pass_type'] = $request->bus_pass_type;
        $data['declaration_1'] = $request->declaration_1;
        $data['declaration_2'] = $request->declaration_2;

        // Add conditional fields based on bus pass type
        if ($request->bus_pass_type === 'daily_travel' || $request->bus_pass_type === 'unmarried_daily_travel') {
            $data['requested_bus_name'] = $request->requested_bus_name;
            $data['destination_from_ahq'] = $request->destination_from_ahq;
        } elseif ($request->bus_pass_type === 'weekend_monthly_travel') {
            $data['living_in_bus'] = $request->living_in_bus;
            $data['destination_location_ahq'] = $request->destination_location_ahq;
            $data['weekend_bus_name'] = $request->weekend_bus_name;
            $data['weekend_destination'] = $request->weekend_destination;
        } elseif ($request->bus_pass_type === 'living_in_only') {
            $data['living_in_bus'] = $request->living_in_bus;
            $data['destination_location_ahq'] = $request->destination_location_ahq;
        } elseif ($request->bus_pass_type === 'weekend_only') {
            $data['weekend_bus_name'] = $request->weekend_bus_name;
            $data['weekend_destination'] = $request->weekend_destination;
        }

        try {
            $bus_pass_application->update($data);
            Log::info('Bus pass application updated successfully', ['id' => $bus_pass_application->id]);

            return redirect()->route('bus-pass-applications.index')
                ->with('success', 'Bus pass application updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update bus pass application', [
                'id' => $bus_pass_application->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update application: ' . $e->getMessage()]);
        }
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

                    // Find rank_id based on rank text from API
                    $rankText = $data['rank'] ?? '';
                    $rank = Rank::where('abb_name', $rankText)->orWhere('full_name', $rankText)->first();
                    $rankId = $rank ? $rank->id : null;

                    $personData = [
                        'rank' => $rankText,
                        'rank_id' => $rankId,
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
