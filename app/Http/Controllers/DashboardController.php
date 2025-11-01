<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BusPassApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $chartData = [];

        try {
            // Get chart data for Branch Subject Clerk
            if (Auth::user() && Auth::user()->hasRole('Bus Pass Subject Clerk (Branch)')) {
                $chartData = $this->getBranchSubjectClerkChartData();
            }
        } catch (\Exception $e) {
            // Log error and continue with empty chart data
            Log::error('Dashboard chart data error: ' . $e->getMessage());
            $chartData = $this->getEmptyChartData();
        }

        return view('dashboard.index', compact('chartData'));
    }

    private function getEmptyChartData()
    {
        return [
            'statusOverview' => [
                'pending_subject_clerk' => 0,
                'pending_staff_officer_branch' => 0,
                'pending_director_branch' => 0,
                'forwarded_to_movement' => 0,
                'approved_for_integration' => 0,
                'rejected' => 0,
            ],
            'monthlyTrends' => [
                'months' => [],
                'created' => [],
                'approved' => []
            ],
            'processingTime' => [
                '1-3 days' => 0,
                '4-7 days' => 0,
                '1-2 weeks' => 0,
                '2+ weeks' => 0
            ],
            'passTypes' => [],
            'weeklyActivity' => [
                'days' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                'created' => [0, 0, 0, 0, 0, 0, 0],
                'forwarded' => [0, 0, 0, 0, 0, 0, 0]
            ],
            'rejectionReasons' => [
                'Incomplete Documents' => 0,
                'Invalid Information' => 0,
                'Policy Violations' => 0,
                'Other Reasons' => 0
            ]
        ];
    }

    private function getBranchSubjectClerkChartData()
    {
        $establishmentId = Auth::user()->establishment_id;

        return [
            'statusOverview' => $this->getStatusOverviewData($establishmentId),
            'monthlyTrends' => $this->getMonthlyTrendsData($establishmentId),
            'processingTime' => $this->getProcessingTimeData($establishmentId),
            'passTypes' => $this->getPassTypesData($establishmentId),
            'weeklyActivity' => $this->getWeeklyActivityData($establishmentId),
            'rejectionReasons' => $this->getRejectionReasonsData($establishmentId)
        ];
    }

    private function getStatusOverviewData($establishmentId)
    {
        $query = BusPassApplication::where('establishment_id', $establishmentId);

        return [
            'pending_subject_clerk' => $query->clone()->where('status', 'pending_subject_clerk')->count(),
            'pending_staff_officer_branch' => $query->clone()->where('status', 'pending_staff_officer_branch')->count(),
            'pending_director_branch' => $query->clone()->where('status', 'pending_director_branch')->count(),
            'forwarded_to_movement' => $query->clone()->where('status', 'forwarded_to_movement')->count(),
            'approved_for_integration' => $query->clone()->where('status', 'approved_for_integration')->count(),
            'rejected' => $query->clone()->where('status', 'rejected')->count(),
        ];
    }

    private function getMonthlyTrendsData($establishmentId)
    {
        $months = [];
        $created = [];
        $approved = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthStart = $date->startOfMonth()->toDateString();
            $monthEnd = $date->endOfMonth()->toDateString();

            $months[] = $date->format('M Y');

            $createdCount = BusPassApplication::where('establishment_id', $establishmentId)
                ->whereBetween('created_at', [$monthStart . ' 00:00:00', $monthEnd . ' 23:59:59'])
                ->count();

            $approvedCount = BusPassApplication::where('establishment_id', $establishmentId)
                ->whereIn('status', ['approved_for_integration', 'approved_for_temp_card', 'integrated_to_branch_card', 'temp_card_printed'])
                ->whereBetween('updated_at', [$monthStart . ' 00:00:00', $monthEnd . ' 23:59:59'])
                ->count();

            $created[] = $createdCount;
            $approved[] = $approvedCount;
        }

        return [
            'months' => $months,
            'created' => $created,
            'approved' => $approved
        ];
    }

    private function getProcessingTimeData($establishmentId)
    {
        $applications = BusPassApplication::where('establishment_id', $establishmentId)
            ->whereIn('status', ['approved_for_integration', 'approved_for_temp_card', 'integrated_to_branch_card', 'temp_card_printed'])
            ->select('created_at', 'updated_at')
            ->get();

        $timeRanges = [
            '1-3 days' => 0,
            '4-7 days' => 0,
            '1-2 weeks' => 0,
            '2+ weeks' => 0
        ];

        foreach ($applications as $app) {
            $days = $app->created_at->diffInDays($app->updated_at);

            if ($days <= 3) {
                $timeRanges['1-3 days']++;
            } elseif ($days <= 7) {
                $timeRanges['4-7 days']++;
            } elseif ($days <= 14) {
                $timeRanges['1-2 weeks']++;
            } else {
                $timeRanges['2+ weeks']++;
            }
        }

        return $timeRanges;
    }

    private function getPassTypesData($establishmentId)
    {
        return BusPassApplication::where('establishment_id', $establishmentId)
            ->select('bus_pass_type', DB::raw('count(*) as count'))
            ->groupBy('bus_pass_type')
            ->get()
            ->pluck('count', 'bus_pass_type')
            ->toArray();
    }

    private function getWeeklyActivityData($establishmentId)
    {
        $weekStart = Carbon::now()->startOfWeek();
        $days = [];
        $created = [];
        $forwarded = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $days[] = $date->format('D');

            $dayStart = $date->startOfDay();
            $dayEnd = $date->endOfDay();

            $createdCount = BusPassApplication::where('establishment_id', $establishmentId)
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->count();

            // Count forwarded applications (status changes from pending_subject_clerk)
            $forwardedCount = BusPassApplication::where('establishment_id', $establishmentId)
                ->where('status', '!=', 'pending_subject_clerk')
                ->whereBetween('updated_at', [$dayStart, $dayEnd])
                ->count();

            $created[] = $createdCount;
            $forwarded[] = $forwardedCount;
        }

        return [
            'days' => $days,
            'created' => $created,
            'forwarded' => $forwarded
        ];
    }

    private function getRejectionReasonsData($establishmentId)
    {
        $rejectedApps = BusPassApplication::where('establishment_id', $establishmentId)
            ->where('status', 'rejected')
            ->whereNotNull('remarks')
            ->get();

        $reasons = [
            'Incomplete Documents' => 0,
            'Invalid Information' => 0,
            'Policy Violations' => 0,
            'Other Reasons' => 0
        ];

        foreach ($rejectedApps as $app) {
            $remarks = strtolower($app->remarks);

            if (str_contains($remarks, 'document') || str_contains($remarks, 'certificate') || str_contains($remarks, 'upload')) {
                $reasons['Incomplete Documents']++;
            } elseif (str_contains($remarks, 'information') || str_contains($remarks, 'data') || str_contains($remarks, 'detail')) {
                $reasons['Invalid Information']++;
            } elseif (str_contains($remarks, 'policy') || str_contains($remarks, 'rule') || str_contains($remarks, 'eligib')) {
                $reasons['Policy Violations']++;
            } else {
                $reasons['Other Reasons']++;
            }
        }

        return $reasons;
    }
}
