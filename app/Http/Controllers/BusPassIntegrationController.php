<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BusPassApplication;
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

        // Base query for approved applications
        $query = BusPassApplication::with(['establishment', 'person'])
            ->whereIn('status', ['approved_for_integration', 'approved_for_temp_card', 'integrated_to_branch_card', 'integrated_to_temp_card']);

        // Filter by route if specified
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

        // Group by establishment and status
        $applications = $query->get()->groupBy('establishment_id');

        $chartData = [];

        foreach ($applications as $establishmentId => $apps) {
            $establishment = $apps->first()->establishment;

            $pendingIntegration = $apps->whereIn('status', ['approved_for_integration', 'approved_for_temp_card'])->count();
            $integrated = $apps->whereIn('status', ['integrated_to_branch_card', 'integrated_to_temp_card'])->count();

            $chartData[] = [
                'establishment_id' => $establishmentId,
                'establishment_name' => $establishment->name ?? 'Unknown',
                'pending_integration' => $pendingIntegration,
                'integrated' => $integrated
            ];
        }

        return response()->json($chartData);
    }

    /**
     * Get applications for a specific establishment
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

        $statusMap = [
            'pending' => ['approved_for_integration', 'approved_for_temp_card'],
            'integrated' => ['integrated_to_branch_card', 'integrated_to_temp_card']
        ];

        $query = BusPassApplication::with(['person', 'establishment'])
            ->where('establishment_id', $establishmentId)
            ->whereIn('status', $statusMap[$type]);

        // Filter by route if specified
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

        return response()->json([
            'data' => $applications->map(function ($app) {
                return [
                    'id' => $app->id,
                    'serial_number' => $app->serial_number,
                    'person_name' => $app->person->name ?? 'N/A',
                    'person_rank' => $app->person->rank ?? 'N/A',
                    'establishment' => $app->establishment->name ?? 'N/A',
                    'requested_bus_name' => $app->requested_bus_name,
                    'weekend_bus_name' => $app->weekend_bus_name,
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
        // Only DMOV users can view application details
        if (!auth()->user()->hasRole(['Subject Clerk (DMOV)', 'Staff Officer 2 (DMOV)', 'Col Mov (DMOV)', 'Director (DMOV)'])) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $application = BusPassApplication::with(['person', 'establishment'])->findOrFail($id);

        return response()->json([
            'application' => $application,
            'person' => $application->person,
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
     * Generate random QR code for temp cards
     */
    private function generateTempCardQR(): string
    {
        do {
            $qrCode = 'TEMP-' . strtoupper(Str::random(10)) . '-' . now()->format('Ymd');
        } while (BusPassApplication::where('temp_card_qr', $qrCode)->exists());

        return $qrCode;
    }
}
