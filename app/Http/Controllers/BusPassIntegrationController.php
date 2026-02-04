<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BusPassApplication;
use App\Models\BusPassApprovalHistory;
use App\Models\BusRoute;
use App\Models\Establishment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BusPassIntegrationController extends Controller
{
    /**
     * Display the integration dashboard
     */
    public function index()
    {
        // Only DMOV users can view this page
        if (!auth()->user()->hasRole(['Subject Clerk (DMOV)', 'Staff Officer 2 (DMOV)', 'Col Mov (DMOV)', 'Director (DMOV)'])) {
            abort(403, 'Access denied. Only DMOV personnel can access this page.');
        }

        $busRoutes = BusRoute::all();
        $livingInBuses = \App\Models\LivingInBuses::all();

        return view('bus-pass-integration.index', compact('busRoutes', 'livingInBuses'));
    }

    /**
     * Get chart data for integration dashboard
     */
    public function getChartData(Request $request): JsonResponse
    {
        // Only DMOV users can view chart data
        if (!auth()->user()->hasRole(['Subject Clerk (DMOV)', 'Staff Officer 2 (DMOV)', 'Col Mov (DMOV)', 'Director (DMOV)'])) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $routeId = $request->get('route_id');

        if ($routeId && $routeId !== 'all') {
            // When a specific route is selected, only show establishments that have applications for that route
            $routeType = $request->get('route_type', 'living_out');

            // Base query for applications with the selected route
            $applicationsQuery = BusPassApplication::with('establishment')
                ->whereIn('status', ['approved_for_integration', 'approved_for_temp_card', 'integrated_to_branch_card', 'integrated_to_temp_card']);

            // Apply route filter
            if ($routeType === 'living_out') {
                $route = BusRoute::find($routeId);
                if ($route) {
                    $applicationsQuery->where(function ($q) use ($route) {
                        $q->where('requested_bus_name', $route->name)
                            ->orWhere('weekend_bus_name', $route->name);
                    });
                }
            } elseif ($routeType === 'living_in') {
                $livingInBus = \App\Models\LivingInBuses::find($routeId);
                if ($livingInBus) {
                    $applicationsQuery->where('living_in_bus', $livingInBus->name);
                }
            }

            // Get distinct establishments that have applications for this route
            $establishmentsWithApps = $applicationsQuery->distinct('establishment_id')
                ->pluck('establishment_id')
                ->toArray();

            $establishments = Establishment::whereIn('id', $establishmentsWithApps)
                ->orderByRaw('seniority_order IS NULL, seniority_order')
                ->orderBy('name')
                ->get();
        } else {
            // When 'all' routes selected, show all establishments
            $establishments = Establishment::orderByRaw('seniority_order IS NULL, seniority_order')
                ->orderBy('name')
                ->get();
        }

        $chartData = [];

        foreach ($establishments as $establishment) {
            // Base query for approved applications for this establishment
            $query = BusPassApplication::where('establishment_id', $establishment->id)
                ->whereIn('status', ['approved_for_integration', 'approved_for_temp_card', 'integrated_to_branch_card', 'integrated_to_temp_card']);

            // Filter by route if specified (only when route is selected)
            if ($routeId && $routeId !== 'all') {
                $routeType = $request->get('route_type', 'living_out');

                if ($routeType === 'living_out') {
                    $route = BusRoute::find($routeId);
                    if ($route) {
                        $query->where(function ($q) use ($route) {
                            $q->where('requested_bus_name', $route->name)
                                ->orWhere('weekend_bus_name', $route->name);
                        });
                    }
                } elseif ($routeType === 'living_in') {
                    $livingInBus = \App\Models\LivingInBuses::find($routeId);
                    if ($livingInBus) {
                        $query->where('living_in_bus', $livingInBus->name);
                    }
                }
            }

            $applications = $query->get();

            $pendingIntegration = $applications->whereIn('status', ['approved_for_integration', 'approved_for_temp_card'])->count();
            $integrated = $applications->whereIn('status', ['integrated_to_branch_card', 'integrated_to_temp_card'])->count();

            $chartData[] = [
                'establishment_id' => $establishment->id,
                'establishment_name' => $establishment->name,
                'pending_integration' => $pendingIntegration,
                'integrated' => $integrated
            ];
        }

        return response()->json($chartData);
    }

    /**
     * Get applications for a specific establishment or all applications
     */
    public function getApplications(Request $request): JsonResponse
    {
        // Only DMOV users can view applications
        if (!auth()->user()->hasRole(['Subject Clerk (DMOV)', 'Staff Officer 2 (DMOV)', 'Col Mov (DMOV)', 'Director (DMOV)'])) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $establishmentId = $request->get('establishment_id');
        $routeId = $request->get('route_id');
        $type = $request->get('type', 'pending'); // pending or integrated
        $all = $request->get('all', false); // New parameter to get all applications

        // Debug logging
        \Log::info('BusPassIntegrationController::getApplications', [
            'establishment_id' => $establishmentId,
            'route_id' => $routeId,
            'route_type' => $request->get('route_type'),
            'type' => $type,
            'all' => $all
        ]);

        $statusMap = [
            'pending' => ['approved_for_integration', 'approved_for_temp_card'],
            'integrated' => ['integrated_to_branch_card', 'integrated_to_temp_card']
        ];

        if ($all) {
            // Get all applications for the selected route
            $query = BusPassApplication::with(['person', 'establishment'])
                ->whereIn('status', array_merge($statusMap['pending'], $statusMap['integrated']));
        } else {
            // Get applications for specific establishment
            $query = BusPassApplication::with(['person', 'establishment'])
                ->where('establishment_id', $establishmentId)
                ->whereIn('status', $statusMap[$type]);
        }

        // Filter by route if specified
        if ($routeId && $routeId !== 'all') {
            $routeType = $request->get('route_type', 'living_out');

            if ($routeType === 'living_out') {
                $route = BusRoute::find($routeId);
                if ($route) {
                    \Log::info('Filtering by living_out route', ['route_id' => $routeId, 'route_name' => $route->name]);
                    $query->where(function ($q) use ($route) {
                        $q->where('requested_bus_name', $route->name)
                            ->orWhere('weekend_bus_name', $route->name);
                    });
                } else {
                    \Log::info('Living out route not found', ['route_id' => $routeId]);
                }
            } elseif ($routeType === 'living_in') {
                $livingInBus = \App\Models\LivingInBuses::find($routeId);
                if ($livingInBus) {
                    \Log::info('Filtering by living_in route', ['route_id' => $routeId, 'route_name' => $livingInBus->name]);
                    $query->where('living_in_bus', $livingInBus->name);
                } else {
                    \Log::info('Living in route not found', ['route_id' => $routeId]);
                }
            }
        } else {
            \Log::info('No route filtering applied', ['route_id' => $routeId]);
        }

        \Log::info('Final query SQL', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);

        // Order by most recently updated first for integrated applications, created_at for pending
        if (!$all && $type === 'integrated') {
            $query->orderBy('updated_at', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $applications = $query->get();

        \Log::info('Applications found', ['count' => $applications->count()]);

        return response()->json([
            'data' => $applications->map(function ($app) {
                return [
                    'id' => $app->id,
                    'serial_number' => $app->serial_number,
                    'person_regiment_no' => $app->person->regiment_no ?? 'N/A',
                    'person_name' => $app->person->name ?? 'N/A',
                    'person_rank' => $app->person->rank ?? 'N/A',
                    'establishment' => $app->establishment->name ?? 'N/A',
                    'bus_pass_type' => $app->bus_pass_type,
                    'requested_bus_name' => $app->requested_bus_name,
                    'destination_from_ahq' => $app->destination_from_ahq,
                    'weekend_bus_name' => $app->weekend_bus_name,
                    'weekend_destination' => $app->weekend_destination,
                    'living_in_bus' => $app->living_in_bus,
                    'destination_location_ahq' => $app->destination_location_ahq,
                    'status' => $app->status,
                    'branch_card_id' => $app->branch_card_id,
                    'temp_card_qr' => $app->temp_card_qr,
                    'created_at' => $app->created_at->format('Y-m-d H:i:s')
                ];
            })
        ]);
    }

    /**
     * Show application details in modal
     */
    public function show($id): JsonResponse
    {
        // Allow DMOV users and branch users to view application details
        $allowedRoles = [
            'Subject Clerk (DMOV)',
            'Staff Officer 2 (DMOV)',
            'Col Mov (DMOV)',
            'Director (DMOV)',
            'Bus Pass Subject Clerk (Branch)',
            'Staff Officer (Branch)'
        ];

        if (!auth()->user()->hasAnyRole($allowedRoles)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $application = BusPassApplication::with(['person', 'establishment'])->findOrFail($id);

        // Get person type name directly
        $personTypeName = null;
        if ($application->person && $application->person->person_type_id) {
            $personType = \App\Models\PersonType::find($application->person->person_type_id);
            $personTypeName = $personType ? $personType->name : null;
        }

        return response()->json([
            'application' => $application,
            'person' => $application->person,
            'person_type_name' => $personTypeName,
            'establishment' => $application->establishment
        ]);
    }

    /**
     * Integrate application (level up status)
     */
    public function integrate(Request $request, $id): JsonResponse
    {
        // Only Director and Col MOV can perform integration
        if (!auth()->user()->hasRole(['Col Mov (DMOV)', 'Director (DMOV)'])) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Only Director and Col MOV can perform integration.'
            ], 403);
        }

        try {
            $application = BusPassApplication::findOrFail($id);

            DB::beginTransaction();

            if ($application->status === 'approved_for_integration') {
                // Branch card integration
                $application->status = 'integrated_to_branch_card';
            } elseif ($application->status === 'approved_for_temp_card') {
                // Temp card integration - generate QR code
                $application->status = 'integrated_to_temp_card';
                $application->temp_card_qr = $this->generateTempCardQR();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Application is not in a valid status for integration'
                ], 400);
            }

            $application->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Application integrated successfully',
                'new_status' => $application->status,
                'temp_card_qr' => $application->temp_card_qr
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Integration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk integrate multiple applications
     */
    public function bulkIntegrate(Request $request): JsonResponse
    {
        // Only Director and Col MOV can perform bulk integration
        if (!auth()->user()->hasRole(['Col Mov (DMOV)', 'Director (DMOV)'])) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Only Director and Col MOV can perform bulk integration.'
            ], 403);
        }

        $request->validate([
            'application_ids' => 'required|array|min:1',
            'application_ids.*' => 'required|integer|exists:bus_pass_applications,id'
        ]);

        $applicationIds = $request->application_ids;
        $successful = 0;
        $failed = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($applicationIds as $id) {
                try {
                    $application = BusPassApplication::findOrFail($id);

                    if ($application->status === 'approved_for_integration') {
                        // Branch card integration
                        $application->status = 'integrated_to_branch_card';
                    } elseif ($application->status === 'approved_for_temp_card') {
                        // Temp card integration - generate QR code
                        $application->status = 'integrated_to_temp_card';
                        $application->temp_card_qr = $this->generateTempCardQR();
                    } else {
                        $failed++;
                        $errors[] = "Application {$id} is not in a valid status for integration";
                        continue;
                    }

                    $application->save();
                    $successful++;
                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "Application {$id}: " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Bulk integration completed. {$successful} application(s) integrated successfully.";
            if ($failed > 0) {
                $message .= " {$failed} application(s) failed.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'successful' => $successful,
                'failed' => $failed,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Bulk integration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Undo integration (revert status)
     */
    public function undoIntegrate(Request $request, $id): JsonResponse
    {
        // Only Director and Col MOV can perform undo integration
        if (!auth()->user()->hasRole(['Col Mov (DMOV)', 'Director (DMOV)'])) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Only Director and Col MOV can undo integration.'
            ], 403);
        }

        try {
            $application = BusPassApplication::findOrFail($id);

            DB::beginTransaction();

            if ($application->status === 'integrated_to_branch_card') {
                // Revert branch card integration
                $application->status = 'approved_for_integration';
            } elseif ($application->status === 'integrated_to_temp_card') {
                // Revert temp card integration - clear QR code
                $application->status = 'approved_for_temp_card';
                $application->temp_card_qr = null;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Application is not in a valid status for undo integration'
                ], 400);
            }

            $application->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Integration undone successfully',
                'new_status' => $application->status
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Undo integration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject integration application and forward to branch staff officer
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'required|string|max:1000'
        ]);

        $application = BusPassApplication::findOrFail($id);

        // Check if application is in pending integration status
        if (!in_array($application->status, ['approved_for_integration', 'approved_for_temp_card'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending integration applications can be rejected'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Store the previous status before updating
            $previousStatus = $application->status;

            // Update application status to rejected for integration
            $application->update([
                'status' => 'rejected_for_integration',
                'integration_rejection_remarks' => $request->remarks,
                'integration_rejected_at' => now(),
                'integration_rejected_by' => auth()->id()
            ]);

            // Create approval history record
            BusPassApprovalHistory::create([
                'bus_pass_application_id' => $application->id,
                'user_id' => auth()->id(),
                'action' => 'integration_rejected',
                'previous_status' => $previousStatus,
                'new_status' => 'rejected_for_integration',
                'remarks' => $request->remarks,
                'action_date' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Application rejected and forwarded to branch staff officer for review',
                'new_status' => $application->status
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Rejection failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate random QR code for temp cards
     */
    private function generateTempCardQR(): string
    {
        do {
            $qrCode = 'TEMP-' . strtoupper(Str::random(20)) . '-' . now()->format('Ymd');
        } while (BusPassApplication::where('temp_card_qr', $qrCode)->exists());

        return $qrCode;
    }
}
