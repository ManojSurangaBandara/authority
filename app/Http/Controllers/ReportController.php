<?php

namespace App\Http\Controllers;

use App\DataTables\HandedOverBusPassApplicationDataTable;
use App\DataTables\IntegratedBusPassApplicationDataTable;
use App\DataTables\IntegratedToBuildCardDataTable;
use App\DataTables\NotyetHandedOverBussPassApplicationDataTable;
use App\DataTables\PendingBusPassApplicationDataTable;
use App\DataTables\RejectedBusPassApplicationDataTable;
use App\DataTables\TemporaryCardPrintedDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Establishment;
use App\Models\BusPassApplication;
use App\Models\BusRoute;
use App\Models\PersonType;
use App\Models\LivingInBuses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function rejected(RejectedBusPassApplicationDataTable $dataTable)
    {
        // Filter establishments for branch users
        $user = Auth::user();
        $branchRoles = ['Bus Pass Subject Clerk (Branch)', 'Staff Officer (Branch)', 'Director (Branch)'];

        if ($user && $user->hasAnyRole($branchRoles) && $user->establishment_id) {
            $establishments = Establishment::where('id', $user->establishment_id)->get();
        } else {
            $establishments = Establishment::all();
        }

        return $dataTable->render('reports.rejected-applications', compact('establishments'));
    }

    public function temporary_card_printed(TemporaryCardPrintedDataTable $dataTable)
    {
        // Filter establishments for branch users
        $user = Auth::user();
        $branchRoles = ['Bus Pass Subject Clerk (Branch)', 'Staff Officer (Branch)', 'Director (Branch)'];

        if ($user && $user->hasAnyRole($branchRoles) && $user->establishment_id) {
            $establishments = Establishment::where('id', $user->establishment_id)->get();
        } else {
            $establishments = Establishment::all();
        }

        return $dataTable->render('reports.temporary-card-printed', compact('establishments'));
    }

    public function handed_over(HandedOverBusPassApplicationDataTable $dataTable)
    {
        // Filter establishments for branch users
        $user = Auth::user();
        $branchRoles = ['Bus Pass Subject Clerk (Branch)', 'Staff Officer (Branch)', 'Director (Branch)'];

        if ($user && $user->hasAnyRole($branchRoles) && $user->establishment_id) {
            $establishments = Establishment::where('id', $user->establishment_id)->get();
        } else {
            $establishments = Establishment::all();
        }

        return $dataTable->render('reports.handed-over-applications', compact('establishments'));
    }

    public function not_yet_handed_over(NotyetHandedOverBussPassApplicationDataTable $dataTable)
    {
        // Filter establishments for branch users
        $user = Auth::user();
        $branchRoles = ['Bus Pass Subject Clerk (Branch)', 'Staff Officer (Branch)', 'Director (Branch)'];

        if ($user && $user->hasAnyRole($branchRoles) && $user->establishment_id) {
            $establishments = Establishment::where('id', $user->establishment_id)->get();
        } else {
            $establishments = Establishment::all();
        }

        return $dataTable->render('reports.not-yet-handed-over-applications', compact('establishments'));
    }

    public function integrated(IntegratedBusPassApplicationDataTable $dataTable)
    {
        // Filter establishments for branch users
        $user = Auth::user();
        $branchRoles = ['Bus Pass Subject Clerk (Branch)', 'Staff Officer (Branch)', 'Director (Branch)'];

        if ($user && $user->hasAnyRole($branchRoles) && $user->establishment_id) {
            $establishments = Establishment::where('id', $user->establishment_id)->get();
        } else {
            $establishments = Establishment::all();
        }

        return $dataTable->render('reports.integrated-applications', compact('establishments'));
    }

    public function pending(PendingBusPassApplicationDataTable $dataTable)
    {
        // Filter establishments for branch users
        $user = Auth::user();
        $branchRoles = ['Bus Pass Subject Clerk (Branch)', 'Staff Officer (Branch)', 'Director (Branch)'];

        if ($user && $user->hasAnyRole($branchRoles) && $user->establishment_id) {
            $establishments = Establishment::where('id', $user->establishment_id)->get();
        } else {
            $establishments = Establishment::all();
        }

        return $dataTable->render('reports.pending-applications', compact('establishments'));
    }


    public function build(IntegratedToBuildCardDataTable $dataTable)
    {
        // Filter establishments for branch users
        $user = Auth::user();
        $branchRoles = ['Bus Pass Subject Clerk (Branch)', 'Staff Officer (Branch)', 'Director (Branch)'];

        if ($user && $user->hasAnyRole($branchRoles) && $user->establishment_id) {
            $establishments = Establishment::where('id', $user->establishment_id)->get();
        } else {
            $establishments = Establishment::all();
        }

        return $dataTable->render('reports.integrated-to-build-card', compact('establishments'));
    }

    /**
     * Display passenger counts report by route and person type
     */
    public function passengerCounts()
    {
        // Get all unique routes from both bus routes and living in buses
        $routeData = collect();

        // Get bus routes from bus_routes table
        $busRoutes = BusRoute::get()
            ->map(function ($route) {
                return (object) [
                    'id' => $route->id,
                    'name' => $route->name,
                    'type' => 'bus_route'
                ];
            });

        // Get living in buses from living_in_buses table
        $livingInBuses = LivingInBuses::get()
            ->map(function ($bus) {
                return (object) [
                    'id' => $bus->id,
                    'name' => $bus->name,
                    'type' => 'living_in_bus'
                ];
            });

        // Combine all routes keeping them separate even if names are same
        $allRoutes = $busRoutes->concat($livingInBuses);

        // Find duplicate route names between different sources
        $busRouteNames = $busRoutes->pluck('name')->toArray();
        $livingInBusNames = $livingInBuses->pluck('name')->toArray();
        $duplicateNames = array_intersect($busRouteNames, $livingInBusNames);

        // Get person types
        $personTypes = PersonType::where('is_active', true)->get();

        // Build route data with passenger counts
        foreach ($allRoutes as $route) {
            $routeName = $route->name;
            $routeType = $route->type;

            $counts = [
                'route' => $routeName,
                'route_type' => $routeType,
                'route_display' => $routeName,
                'has_duplicate' => in_array($routeName, $duplicateNames)
            ];

            // Get seating capacity for this route
            $routeTypeForCapacity = $routeType === 'bus_route' ? 'living_out' : 'living_in';
            $capacityInfo = (new BusPassApplication())->getSeatingCapacityForRoute($routeName, $routeTypeForCapacity);
            $counts['seating_capacity'] = $capacityInfo ? $capacityInfo['seats'] : null;

            // Initialize counts for each person type
            foreach ($personTypes as $personType) {
                $counts[strtolower($personType->name)] = 0;
            }

            // Count passengers by person type for this route based on route type
            if ($routeType === 'bus_route') {
                // For bus routes, count from requested_bus_name and weekend_bus_name
                $applications = BusPassApplication::with(['person.personType'])
                    ->where(function ($query) use ($routeName) {
                        $query->where('requested_bus_name', $routeName)
                            ->orWhere('weekend_bus_name', $routeName);
                    })
                    ->whereIn('status', ['integrated_to_branch_card', 'temp_card_handed_over'])
                    ->get();
            } else {
                // For living in buses, count from living_in_bus
                $applications = BusPassApplication::with(['person.personType'])
                    ->where('living_in_bus', $routeName)
                    ->whereIn('status', ['integrated_to_branch_card', 'temp_card_handed_over'])
                    ->get();
            }

            foreach ($applications as $application) {
                if ($application->person && $application->person->personType) {
                    $personTypeName = strtolower($application->person->personType->name);
                    if (isset($counts[$personTypeName])) {
                        $counts[$personTypeName]++;
                    }
                }
            }

            // Calculate total
            $counts['total'] = array_sum(array_filter($counts, function ($value, $key) {
                return is_numeric($value) && $key !== 'seating_capacity';
            }, ARRAY_FILTER_USE_BOTH));

            $routeData->push($counts);
        }

        // Sort by route name
        $routeData = $routeData->sortBy('route')->values();

        return view('reports.passenger-counts', compact('routeData', 'personTypes'));
    }

    /**
     * Display establishment wise applications report
     */
    public function establishmentWiseApplications(Request $request)
    {
        // Get all establishments for dropdown
        $establishments = Establishment::orderBy('name')->get();

        // Filter by establishment if selected
        $selectedEstablishment = $request->get('establishment_id');

        // Base query for applications with required status
        $applicationsQuery = BusPassApplication::with([
            'person.personType',
            'establishment'
        ])
            ->whereIn('status', ['integrated_to_branch_card', 'temp_card_handed_over']);

        // Apply establishment filter if selected
        if ($selectedEstablishment) {
            $applicationsQuery->where('establishment_id', $selectedEstablishment);
        }

        $applications = $applicationsQuery->get();

        // Get person types for totals calculation
        $personTypes = PersonType::where('is_active', true)->get();

        // Calculate totals by person type
        $totals = [];
        foreach ($personTypes as $personType) {
            $totals[strtolower($personType->name)] = $applications->filter(function ($app) use ($personType) {
                return $app->person && $app->person->personType &&
                    $app->person->personType->id === $personType->id;
            })->count();
        }

        return view('reports.establishment-wise-applications', compact(
            'applications',
            'establishments',
            'selectedEstablishment',
            'personTypes',
            'totals'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
