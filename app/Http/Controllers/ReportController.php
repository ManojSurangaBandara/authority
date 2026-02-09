<?php

namespace App\Http\Controllers;

use App\DataTables\HandedOverBusPassApplicationDataTable;
use App\DataTables\IntegratedBusPassApplicationDataTable;
use App\DataTables\IntegratedToBuildCardDataTable;
use App\DataTables\IncidentReportsDataTable;
use App\DataTables\NotyetHandedOverBussPassApplicationDataTable;
use App\DataTables\OnboardedPassengersDataTable;
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
use App\Models\IncidentType;
use App\Models\Incident;
use App\Models\Trip;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\DataTables\TripsDataTable;

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

    public function onboarded_passengers(OnboardedPassengersDataTable $dataTable)
    {
        $busRoutes = \App\Models\BusRoute::all()->map(function ($r) {
            return ['id' => 'route_' . $r->id, 'name' => $r->name . ' (Living Out)'];
        });
        $livingInRoutes = \App\Models\LivingInBuses::all()->map(function ($l) {
            return ['id' => 'living_' . $l->id, 'name' => $l->name . ' (Living In)'];
        });
        $routes = $busRoutes->merge($livingInRoutes);

        $types = ['morning' => 'Morning', 'evening' => 'Evening'];

        return $dataTable->render('reports.onboarded-passengers', compact('routes', 'types'));
    }

    public function incident_reports(IncidentReportsDataTable $dataTable)
    {
        $busRoutes = BusRoute::all()->map(function ($r) {
            return ['id' => 'route_' . $r->id, 'name' => $r->name . ' (Living Out)'];
        });
        $livingInRoutes = LivingInBuses::all()->map(function ($l) {
            return ['id' => 'living_' . $l->id, 'name' => $l->name . ' (Living In)'];
        });
        $routes = $busRoutes->merge($livingInRoutes);

        $types = IncidentType::pluck('name', 'id')->toArray();
        $tripTypes = ['morning' => 'Morning', 'evening' => 'Evening'];

        return $dataTable->render('reports.incident-reports', compact('routes', 'types', 'tripTypes'));
    }

    public function show_incident($id)
    {
        $incident = Incident::with(['incidentType', 'trip.escort', 'trip.driver', 'trip.bus', 'trip.slcmpIncharge'])->findOrFail($id);

        return view('reports.incident-detail', compact('incident'));
    }

    public function trips(TripsDataTable $dataTable)
    {
        $busRoutes = BusRoute::all()->map(function ($r) {
            return ['id' => 'route_' . $r->id, 'name' => $r->name . ' (Living Out)'];
        });
        $livingInRoutes = LivingInBuses::all()->map(function ($l) {
            return ['id' => 'living_' . $l->id, 'name' => $l->name . ' (Living In)'];
        });
        $routes = $busRoutes->merge($livingInRoutes);

        $tripTypes = ['morning' => 'Morning', 'evening' => 'Evening'];

        return $dataTable->render('reports.trips', compact('routes', 'tripTypes'));
    }

    public function tripMap($id)
    {
        $trip = Trip::with(['escort', 'driver', 'bus', 'slcmpIncharge', 'tripLocations'])
            ->leftJoin('bus_routes', function ($join) {
                $join->on('trips.bus_route_id', '=', 'bus_routes.id')
                    ->where('trips.route_type', '=', 'living_out');
            })
            ->leftJoin('living_in_buses', function ($join) {
                $join->on('trips.bus_route_id', '=', 'living_in_buses.id')
                    ->where('trips.route_type', '=', 'living_in');
            })
            ->select('trips.*', 'bus_routes.name as bus_route_name', 'living_in_buses.name as living_in_bus_name')
            ->findOrFail($id);
        $isOngoing = is_null($trip->trip_end_time);

        return view('reports.trip-map', compact('trip', 'isOngoing'));
    }

    public function getTripLocations($id)
    {
        $trip = Trip::findOrFail($id);
        $locations = $trip->tripLocations()->select('latitude', 'longitude', 'recorded_at')->get();

        return response()->json([
            'success' => true,
            'data' => $locations
        ]);
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
                    ->whereIn('status', ['integrated_to_branch_card', 'integrated_to_temp_card'])
                    ->get();
            } else {
                // For living in buses, count from living_in_bus
                $applications = BusPassApplication::with(['person.personType'])
                    ->where('living_in_bus', $routeName)
                    ->whereIn('status', ['integrated_to_branch_card', 'integrated_to_temp_card'])
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
     * Display living out passenger counts report by route and person type
     */
    public function livingOutPassengerCounts()
    {
        // Get only bus routes (living out routes)
        $busRoutes = BusRoute::get()
            ->map(function ($route) {
                return (object) [
                    'id' => $route->id,
                    'name' => $route->name,
                    'type' => 'bus_route'
                ];
            });

        // Get person types
        $personTypes = PersonType::where('is_active', true)->get();

        // Build route data with passenger counts
        $routeData = collect();
        foreach ($busRoutes as $route) {
            $routeName = $route->name;
            $routeType = $route->type;

            $counts = [
                'route' => $routeName,
                'route_type' => $routeType,
                'route_display' => $routeName,
                'has_duplicate' => false
            ];

            // Get seating capacity for this route
            $capacityInfo = (new BusPassApplication())->getSeatingCapacityForRoute($routeName, 'living_out');
            $counts['seating_capacity'] = $capacityInfo ? $capacityInfo['seats'] : null;

            // Initialize counts for each person type
            foreach ($personTypes as $personType) {
                $counts[strtolower($personType->name)] = 0;
            }

            // Count passengers by person type for this route
            $applications = BusPassApplication::with(['person.personType'])
                ->where(function ($query) use ($routeName) {
                    $query->where('requested_bus_name', $routeName)
                        ->orWhere('weekend_bus_name', $routeName);
                })
                ->whereIn('status', ['integrated_to_branch_card', 'integrated_to_temp_card'])
                ->get();

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

        return view('reports.living-out-passenger-counts', compact('routeData', 'personTypes'));
    }

    /**
     * Display living in passenger counts report by route and person type
     */
    public function livingInPassengerCounts()
    {
        // Get only living in buses
        $livingInBuses = LivingInBuses::get()
            ->map(function ($bus) {
                return (object) [
                    'id' => $bus->id,
                    'name' => $bus->name,
                    'type' => 'living_in_bus'
                ];
            });

        // Get person types
        $personTypes = PersonType::where('is_active', true)->get();

        // Build route data with passenger counts
        $routeData = collect();
        foreach ($livingInBuses as $route) {
            $routeName = $route->name;
            $routeType = $route->type;

            $counts = [
                'route' => $routeName,
                'route_type' => $routeType,
                'route_display' => $routeName,
                'has_duplicate' => false
            ];

            // Get seating capacity for this route
            $capacityInfo = (new BusPassApplication())->getSeatingCapacityForRoute($routeName, 'living_in');
            $counts['seating_capacity'] = $capacityInfo ? $capacityInfo['seats'] : null;

            // Initialize counts for each person type
            foreach ($personTypes as $personType) {
                $counts[strtolower($personType->name)] = 0;
            }

            // Count passengers by person type for this route
            $applications = BusPassApplication::with(['person.personType'])
                ->where('living_in_bus', $routeName)
                ->whereIn('status', ['integrated_to_branch_card', 'integrated_to_temp_card'])
                ->get();

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

        return view('reports.living-in-passenger-counts', compact('routeData', 'personTypes'));
    }

    /**
     * Display route establishment report
     */
    public function routeEstablishmentReport(Request $request)
    {
        // Get all bus routes and living in buses for dropdown
        $busRoutes = BusRoute::orderBy('name')->get();
        $livingInBuses = LivingInBuses::orderBy('name')->get();

        // Get selected route
        $selectedRoute = $request->get('route_name');

        // Get all establishments
        $establishments = Establishment::orderBy('name')->get();

        $reportData = collect();

        if ($selectedRoute) {
            foreach ($establishments as $establishment) {
                // Build base query for this establishment
                $baseQuery = BusPassApplication::where('establishment_id', $establishment->id);

                // Apply route filter if not "all"
                if ($selectedRoute !== 'all') {
                    $baseQuery->where(function ($query) use ($selectedRoute) {
                        $query->where('requested_bus_name', $selectedRoute)
                            ->orWhere('weekend_bus_name', $selectedRoute)
                            ->orWhere('living_in_bus', $selectedRoute);
                    });
                }

                // Count all applications for this establishment and route
                $allCount = (clone $baseQuery)->count();

                // Count pending applications at branch level
                $pendingBranchCount = (clone $baseQuery)
                    ->whereIn('status', ['pending_subject_clerk', 'pending_staff_officer_branch'])
                    ->count();

                // Count pending applications at DMOV level
                $pendingDMOVCount = (clone $baseQuery)
                    ->whereIn('status', ['forwarded_to_movement', 'pending_staff_officer_2_mov', 'pending_col_mov'])
                    ->count();

                // Count approved applications (approved by Col Mov or Director Mov but not yet integrated)
                $approvedCount = (clone $baseQuery)
                    ->whereIn('status', ['approved_for_integration', 'approved_for_temp_card'])
                    ->count();

                // Count integrated applications
                $integratedCount = (clone $baseQuery)
                    ->whereIn('status', ['integrated_to_branch_card', 'integrated_to_temp_card', 'temp_card_handed_over', 'temp_card_printed'])
                    ->count();

                // Count rejected applications
                $rejectedCount = (clone $baseQuery)
                    ->where('status', 'rejected')
                    ->count();

                $reportData->push([
                    'establishment' => $establishment->name,
                    'all' => $allCount,
                    'pending_branch' => $pendingBranchCount,
                    'pending_dmov' => $pendingDMOVCount,
                    'approved' => $approvedCount,
                    'integrated' => $integratedCount,
                    'rejected' => $rejectedCount
                ]);
            }
        }

        // Handle Excel export
        if ($request->get('export') === 'excel' && $selectedRoute) {
            return $this->exportRouteEstablishmentReport($reportData, $selectedRoute);
        }

        return view('reports.route-establishment-report', compact('busRoutes', 'livingInBuses', 'selectedRoute', 'reportData'));
    }

    /**
     * Export route establishment report to Excel (CSV format)
     */
    private function exportRouteEstablishmentReport($reportData, $routeName)
    {
        $displayName = $routeName === 'all' ? 'All_Routes' : str_replace(' ', '_', $routeName);
        $filename = 'route_establishment_report_' . $displayName . '_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($reportData, $routeName) {
            $file = fopen('php://output', 'w');

            // Add BOM for Excel UTF-8 recognition
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Write header
            $reportTitle = $routeName === 'all' ? 'All Routes' : $routeName;
            fputcsv($file, ['Route Establishment Report - ' . $reportTitle]);
            fputcsv($file, []); // Empty row
            fputcsv($file, ['Establishment', 'All', 'Pending-Branch', 'Pending-DMOV', 'Approved', 'Integrated', 'Rejected']);

            // Write data
            foreach ($reportData as $row) {
                fputcsv($file, [
                    $row['establishment'],
                    $row['all'],
                    $row['pending_branch'],
                    $row['pending_dmov'],
                    $row['approved'],
                    $row['integrated'],
                    $row['rejected']
                ]);
            }

            // Write totals
            fputcsv($file, []); // Empty row
            fputcsv($file, [
                'Total',
                $reportData->sum('all'),
                $reportData->sum('pending_branch'),
                $reportData->sum('pending_dmov'),
                $reportData->sum('approved'),
                $reportData->sum('integrated'),
                $reportData->sum('rejected')
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
            ->whereIn('status', ['integrated_to_branch_card', 'integrated_to_temp_card', 'temp_card_handed_over', 'temp_card_printed']);

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
