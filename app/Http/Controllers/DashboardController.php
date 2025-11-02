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
            // Get chart data for Staff Officer (Branch)
            elseif (Auth::user() && Auth::user()->hasRole('Staff Officer (Branch)')) {
                $chartData = $this->getBranchStaffOfficerChartData();
            }
            // Get chart data for Director (Branch)
            elseif (Auth::user() && Auth::user()->hasRole('Director (Branch)')) {
                $chartData = $this->getBranchDirectorChartData();
            }
            // Get chart data for DMOV Subject Clerk
            elseif (Auth::user() && Auth::user()->hasRole('Subject Clerk (DMOV)')) {
                $chartData = $this->getDmovSubjectClerkChartData();
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

    private function getBranchStaffOfficerChartData()
    {
        $establishmentId = Auth::user()->establishment_id;

        return [
            'approvalOverview' => $this->getStaffOfficerApprovalOverview($establishmentId),
            'monthlyApprovals' => $this->getStaffOfficerMonthlyApprovals($establishmentId),
            'approvalTime' => $this->getStaffOfficerApprovalTime($establishmentId),
            'recommendationStatus' => $this->getRecommendationStatusData($establishmentId),
            'weeklyRecommendations' => $this->getWeeklyRecommendationsData($establishmentId)
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

    // Staff Officer specific chart data methods
    private function getStaffOfficerApprovalOverview($establishmentId)
    {
        $query = BusPassApplication::where('establishment_id', $establishmentId);

        return [
            'pending_review' => $query->clone()->where('status', 'pending_staff_officer_branch')->count(),
            'recommended' => $query->clone()
                ->whereHas('approvalHistory', function ($q) {
                    $q->where('action', 'recommended')
                        ->where('user_id', Auth::id());
                })->count(),
            'not_recommended' => $query->clone()
                ->whereHas('approvalHistory', function ($q) {
                    $q->where('action', 'not_recommended')
                        ->where('user_id', Auth::id());
                })->count(),
            'pending_director' => $query->clone()->where('status', 'pending_director_branch')->count(),
            'approved' => $query->clone()->whereIn('status', ['forwarded_to_movement', 'approved_for_integration', 'approved_for_temp_card'])->count(),
        ];
    }

    private function getStaffOfficerMonthlyApprovals($establishmentId)
    {
        $months = [];
        $received = [];
        $recommended = [];
        $notRecommended = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthStart = $date->startOfMonth()->toDateString();
            $monthEnd = $date->endOfMonth()->toDateString();

            $months[] = $date->format('M Y');

            // Applications received for review
            $receivedCount = BusPassApplication::where('establishment_id', $establishmentId)
                ->where('status', 'pending_staff_officer_branch')
                ->whereBetween('updated_at', [$monthStart . ' 00:00:00', $monthEnd . ' 23:59:59'])
                ->count();

            // Recommended by this staff officer
            $recommendedCount = DB::table('bus_pass_approval_histories')
                ->join('bus_pass_applications', 'bus_pass_approval_histories.bus_pass_application_id', '=', 'bus_pass_applications.id')
                ->where('bus_pass_applications.establishment_id', $establishmentId)
                ->where('bus_pass_approval_histories.user_id', Auth::id())
                ->where('bus_pass_approval_histories.action', 'recommended')
                ->whereBetween('bus_pass_approval_histories.action_date', [$monthStart . ' 00:00:00', $monthEnd . ' 23:59:59'])
                ->count();

            // Not recommended by this staff officer
            $notRecommendedCount = DB::table('bus_pass_approval_histories')
                ->join('bus_pass_applications', 'bus_pass_approval_histories.bus_pass_application_id', '=', 'bus_pass_applications.id')
                ->where('bus_pass_applications.establishment_id', $establishmentId)
                ->where('bus_pass_approval_histories.user_id', Auth::id())
                ->where('bus_pass_approval_histories.action', 'not_recommended')
                ->whereBetween('bus_pass_approval_histories.action_date', [$monthStart . ' 00:00:00', $monthEnd . ' 23:59:59'])
                ->count();

            $received[] = $receivedCount;
            $recommended[] = $recommendedCount;
            $notRecommended[] = $notRecommendedCount;
        }

        return [
            'labels' => $months,
            'received' => $received,
            'recommended' => $recommended,
            'not_recommended' => $notRecommended
        ];
    }

    private function getStaffOfficerApprovalTime($establishmentId)
    {
        // Get applications this staff officer has acted on
        $approvalHistories = DB::table('bus_pass_approval_histories')
            ->join('bus_pass_applications', 'bus_pass_approval_histories.bus_pass_application_id', '=', 'bus_pass_applications.id')
            ->where('bus_pass_applications.establishment_id', $establishmentId)
            ->where('bus_pass_approval_histories.user_id', Auth::id())
            ->whereIn('bus_pass_approval_histories.action', ['recommended', 'not_recommended'])
            ->select('bus_pass_applications.created_at', 'bus_pass_approval_histories.action_date')
            ->get();

        $timeRanges = [
            'same_day' => 0,
            'one_to_two' => 0,
            'three_to_five' => 0,
            'over_five' => 0
        ];

        foreach ($approvalHistories as $history) {
            $created = Carbon::parse($history->created_at);
            $actionDate = Carbon::parse($history->action_date);
            $days = $created->diffInDays($actionDate);

            if ($days == 0) {
                $timeRanges['same_day']++;
            } elseif ($days <= 2) {
                $timeRanges['one_to_two']++;
            } elseif ($days <= 5) {
                $timeRanges['three_to_five']++;
            } else {
                $timeRanges['over_five']++;
            }
        }

        return $timeRanges;
    }

    private function getRecommendationStatusData($establishmentId)
    {
        // Applications recommended by staff officer
        $recommended = DB::table('bus_pass_approval_histories')
            ->join('bus_pass_applications', 'bus_pass_approval_histories.bus_pass_application_id', '=', 'bus_pass_applications.id')
            ->where('bus_pass_applications.establishment_id', $establishmentId)
            ->where('bus_pass_approval_histories.user_id', Auth::id())
            ->where('bus_pass_approval_histories.action', 'recommended')
            ->select('bus_pass_applications.status', DB::raw('count(*) as count'))
            ->groupBy('bus_pass_applications.status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'pending_director' => $recommended['pending_director_branch'] ?? 0,
            'approved_by_director' => ($recommended['forwarded_to_movement'] ?? 0) + ($recommended['approved_for_integration'] ?? 0) + ($recommended['approved_for_temp_card'] ?? 0),
            'rejected_by_director' => $recommended['rejected'] ?? 0
        ];
    }

    private function getWeeklyRecommendationsData($establishmentId)
    {
        $weekStart = Carbon::now()->startOfWeek();
        $days = [];
        $data = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $days[] = $date->format('D');

            $dayStart = $date->startOfDay();
            $dayEnd = $date->endOfDay();

            // Total recommendations (both recommended and not recommended) for this day
            $totalCount = DB::table('bus_pass_approval_histories')
                ->join('bus_pass_applications', 'bus_pass_approval_histories.bus_pass_application_id', '=', 'bus_pass_applications.id')
                ->where('bus_pass_applications.establishment_id', $establishmentId)
                ->where('bus_pass_approval_histories.user_id', Auth::id())
                ->whereIn('bus_pass_approval_histories.action', ['recommended', 'not_recommended'])
                ->whereBetween('bus_pass_approval_histories.action_date', [$dayStart, $dayEnd])
                ->count();

            $data[] = $totalCount;
        }

        return [
            'labels' => $days,
            'data' => $data
        ];
    }

    // Branch Director Dashboard Methods
    private function getBranchDirectorChartData()
    {
        $establishmentId = Auth::user()->establishment_id;
        Log::info('Building Director chart data for establishment: ' . $establishmentId);

        try {
            $approvalOverview = $this->getDirectorApprovalOverview($establishmentId);
            Log::info('Approval overview data: ' . json_encode($approvalOverview));

            $monthlyApprovals = $this->getDirectorMonthlyApprovals($establishmentId);
            Log::info('Monthly approvals data keys: ' . implode(', ', array_keys($monthlyApprovals)));

            $approvalTime = $this->getDirectorApprovalTime($establishmentId);
            Log::info('Approval time data: ' . json_encode($approvalTime));

            $passTypeDistribution = $this->getDirectorPassTypeDistribution($establishmentId);
            Log::info('Pass type distribution: ' . json_encode($passTypeDistribution));

            $weeklyApprovals = $this->getDirectorWeeklyApprovals($establishmentId);
            Log::info('Weekly approvals keys: ' . implode(', ', array_keys($weeklyApprovals)));

            return [
                'approvalOverview' => $approvalOverview,
                'monthlyApprovals' => $monthlyApprovals,
                'approvalTime' => $approvalTime,
                'passTypeDistribution' => $passTypeDistribution,
                'weeklyApprovals' => $weeklyApprovals
            ];
        } catch (\Exception $e) {
            Log::error('Error in getBranchDirectorChartData: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }

    private function getDirectorApprovalOverview($establishmentId)
    {
        // Get applications approved/rejected by this director
        $pendingReview = BusPassApplication::where('establishment_id', $establishmentId)
            ->where('status', 'pending_director_branch')
            ->count();

        $approved = DB::table('bus_pass_approval_histories')
            ->join('bus_pass_applications', 'bus_pass_approval_histories.bus_pass_application_id', '=', 'bus_pass_applications.id')
            ->where('bus_pass_applications.establishment_id', $establishmentId)
            ->where('bus_pass_approval_histories.user_id', Auth::id())
            ->where('bus_pass_approval_histories.action', 'approved')
            ->count();

        $rejected = DB::table('bus_pass_approval_histories')
            ->join('bus_pass_applications', 'bus_pass_approval_histories.bus_pass_application_id', '=', 'bus_pass_applications.id')
            ->where('bus_pass_applications.establishment_id', $establishmentId)
            ->where('bus_pass_approval_histories.user_id', Auth::id())
            ->where('bus_pass_approval_histories.action', 'rejected')
            ->count();

        $forwardedToMovement = BusPassApplication::where('establishment_id', $establishmentId)
            ->where('status', 'forwarded_to_movement')
            ->count();

        $totalProcessed = $approved + $rejected;

        return [
            'pending_review' => $pendingReview,
            'approved' => $approved,
            'rejected' => $rejected,
            'forwarded_to_movement' => $forwardedToMovement,
            'total_processed' => $totalProcessed
        ];
    }

    private function getDirectorMonthlyApprovals($establishmentId)
    {
        $months = [];
        $received = [];
        $approved = [];
        $rejected = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthStart = $date->startOfMonth()->toDateString();
            $monthEnd = $date->endOfMonth()->toDateString();

            $months[] = $date->format('M Y');

            // Applications received for director review
            $receivedCount = BusPassApplication::where('establishment_id', $establishmentId)
                ->where('status', 'pending_director_branch')
                ->whereBetween('updated_at', [$monthStart . ' 00:00:00', $monthEnd . ' 23:59:59'])
                ->count();

            // Approved by this director
            $approvedCount = DB::table('bus_pass_approval_histories')
                ->join('bus_pass_applications', 'bus_pass_approval_histories.bus_pass_application_id', '=', 'bus_pass_applications.id')
                ->where('bus_pass_applications.establishment_id', $establishmentId)
                ->where('bus_pass_approval_histories.user_id', Auth::id())
                ->where('bus_pass_approval_histories.action', 'approved')
                ->whereBetween('bus_pass_approval_histories.action_date', [$monthStart . ' 00:00:00', $monthEnd . ' 23:59:59'])
                ->count();

            // Rejected by this director
            $rejectedCount = DB::table('bus_pass_approval_histories')
                ->join('bus_pass_applications', 'bus_pass_approval_histories.bus_pass_application_id', '=', 'bus_pass_applications.id')
                ->where('bus_pass_applications.establishment_id', $establishmentId)
                ->where('bus_pass_approval_histories.user_id', Auth::id())
                ->where('bus_pass_approval_histories.action', 'rejected')
                ->whereBetween('bus_pass_approval_histories.action_date', [$monthStart . ' 00:00:00', $monthEnd . ' 23:59:59'])
                ->count();

            $received[] = $receivedCount;
            $approved[] = $approvedCount;
            $rejected[] = $rejectedCount;
        }

        return [
            'labels' => $months,
            'received' => $received,
            'approved' => $approved,
            'rejected' => $rejected
        ];
    }

    private function getDirectorApprovalTime($establishmentId)
    {
        // Get applications this director has acted on
        $approvalHistories = DB::table('bus_pass_approval_histories')
            ->join('bus_pass_applications', 'bus_pass_approval_histories.bus_pass_application_id', '=', 'bus_pass_applications.id')
            ->where('bus_pass_applications.establishment_id', $establishmentId)
            ->where('bus_pass_approval_histories.user_id', Auth::id())
            ->whereIn('bus_pass_approval_histories.action', ['approved', 'rejected'])
            ->select('bus_pass_applications.created_at', 'bus_pass_approval_histories.action_date')
            ->get();

        $timeRanges = [
            'same_day' => 0,
            'one_to_two' => 0,
            'three_to_five' => 0,
            'over_five' => 0
        ];

        foreach ($approvalHistories as $history) {
            $created = Carbon::parse($history->created_at);
            $actionDate = Carbon::parse($history->action_date);
            $days = $created->diffInDays($actionDate);

            if ($days == 0) {
                $timeRanges['same_day']++;
            } elseif ($days <= 2) {
                $timeRanges['one_to_two']++;
            } elseif ($days <= 5) {
                $timeRanges['three_to_five']++;
            } else {
                $timeRanges['over_five']++;
            }
        }

        return $timeRanges;
    }

    private function getDirectorPassTypeDistribution($establishmentId)
    {
        // Get pass types for applications approved by this director
        $passTypes = DB::table('bus_pass_approval_histories')
            ->join('bus_pass_applications', 'bus_pass_approval_histories.bus_pass_application_id', '=', 'bus_pass_applications.id')
            ->where('bus_pass_applications.establishment_id', $establishmentId)
            ->where('bus_pass_approval_histories.user_id', Auth::id())
            ->where('bus_pass_approval_histories.action', 'approved')
            ->select('bus_pass_applications.bus_pass_type', DB::raw('count(*) as count'))
            ->groupBy('bus_pass_applications.bus_pass_type')
            ->pluck('count', 'bus_pass_type')
            ->toArray();

        return [
            'daily_travel' => $passTypes['daily_travel'] ?? 0,
            'weekend_monthly_travel' => $passTypes['weekend_monthly_travel'] ?? 0
        ];
    }

    private function getDirectorWeeklyApprovals($establishmentId)
    {
        $weekStart = Carbon::now()->startOfWeek();
        $days = [];
        $data = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $days[] = $date->format('D');

            $dayStart = $date->startOfDay();
            $dayEnd = $date->endOfDay();

            // Total approvals/rejections for this day
            $totalCount = DB::table('bus_pass_approval_histories')
                ->join('bus_pass_applications', 'bus_pass_approval_histories.bus_pass_application_id', '=', 'bus_pass_applications.id')
                ->where('bus_pass_applications.establishment_id', $establishmentId)
                ->where('bus_pass_approval_histories.user_id', Auth::id())
                ->whereIn('bus_pass_approval_histories.action', ['approved', 'rejected'])
                ->whereBetween('bus_pass_approval_histories.action_date', [$dayStart, $dayEnd])
                ->count();

            $data[] = $totalCount;
        }

        return [
            'labels' => $days,
            'data' => $data
        ];
    }

    // DMOV Subject Clerk Dashboard Methods
    private function getDmovSubjectClerkChartData()
    {
        return [
            'overallStatus' => $this->getDmovOverallStatus(),
            'branchWiseApplications' => $this->getBranchWiseApplications(),
            'monthlyTrends' => $this->getDmovMonthlyTrends(),
            'processingTime' => $this->getDmovProcessingTime(),
            'passTypeDistribution' => $this->getDmovPassTypeDistribution(),
            'establishmentPerformance' => $this->getEstablishmentPerformance()
        ];
    }

    private function getDmovOverallStatus()
    {
        return [
            'forwarded_to_movement' => BusPassApplication::where('status', 'forwarded_to_movement')->count(),
            'pending_dmov_subject_clerk' => BusPassApplication::where('status', 'pending_dmov_subject_clerk')->count(),
            'pending_dmov_staff_officer_2' => BusPassApplication::where('status', 'pending_dmov_staff_officer_2')->count(),
            'pending_dmov_staff_officer_1' => BusPassApplication::where('status', 'pending_dmov_staff_officer_1')->count(),
            'approved_for_integration' => BusPassApplication::where('status', 'approved_for_integration')->count(),
            'rejected' => BusPassApplication::where('status', 'rejected')->count()
        ];
    }

    private function getBranchWiseApplications()
    {
        $branchData = DB::table('bus_pass_applications')
            ->join('establishments', 'bus_pass_applications.establishment_id', '=', 'establishments.id')
            ->select('establishments.name as branch_name', DB::raw('count(*) as total'))
            ->groupBy('establishments.id', 'establishments.name')
            ->get();

        $labels = [];
        $data = [];

        foreach ($branchData as $branch) {
            $labels[] = $branch->branch_name;
            $data[] = $branch->total;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getDmovMonthlyTrends()
    {
        $months = [];
        $received = [];
        $processed = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthStart = $date->startOfMonth()->toDateString();
            $monthEnd = $date->endOfMonth()->toDateString();

            $months[] = $date->format('M Y');

            // Applications received from branches (forwarded_to_movement)
            $receivedCount = BusPassApplication::where('status', 'forwarded_to_movement')
                ->whereBetween('updated_at', [$monthStart . ' 00:00:00', $monthEnd . ' 23:59:59'])
                ->count();

            // Applications processed by DMOV (approved or rejected)
            $processedCount = BusPassApplication::whereIn('status', ['approved_for_integration', 'rejected'])
                ->whereBetween('updated_at', [$monthStart . ' 00:00:00', $monthEnd . ' 23:59:59'])
                ->count();

            $received[] = $receivedCount;
            $processed[] = $processedCount;
        }

        return [
            'labels' => $months,
            'received' => $received,
            'processed' => $processed
        ];
    }

    private function getDmovProcessingTime()
    {
        // Get applications that have been processed by DMOV
        $processedApps = BusPassApplication::whereIn('status', ['approved_for_integration', 'rejected'])
            ->where('updated_at', '>=', Carbon::now()->subMonths(6))
            ->get();

        $timeRanges = [
            'same_day' => 0,
            'one_to_two' => 0,
            'three_to_five' => 0,
            'over_five' => 0
        ];

        foreach ($processedApps as $app) {
            // Calculate time from when it was forwarded to movement to final status
            $forwardedDate = $app->updated_at; // Approximation - when status changed to forwarded
            $processedDate = $app->updated_at; // Final update date

            // For better accuracy, we should track when it actually reached DMOV
            // For now, using created_at to updated_at difference
            $days = $app->created_at->diffInDays($app->updated_at);

            if ($days == 0) {
                $timeRanges['same_day']++;
            } elseif ($days <= 2) {
                $timeRanges['one_to_two']++;
            } elseif ($days <= 5) {
                $timeRanges['three_to_five']++;
            } else {
                $timeRanges['over_five']++;
            }
        }

        return $timeRanges;
    }

    private function getDmovPassTypeDistribution()
    {
        $passTypes = BusPassApplication::select('bus_pass_type', DB::raw('count(*) as count'))
            ->whereIn('status', ['forwarded_to_movement', 'pending_dmov_subject_clerk', 'pending_dmov_staff_officer_2', 'pending_dmov_staff_officer_1', 'approved_for_integration'])
            ->groupBy('bus_pass_type')
            ->pluck('count', 'bus_pass_type')
            ->toArray();

        return [
            'daily_travel' => $passTypes['daily_travel'] ?? 0,
            'weekend_monthly_travel' => $passTypes['weekend_monthly_travel'] ?? 0
        ];
    }

    private function getEstablishmentPerformance()
    {
        // Get performance data by establishment (approval rates, processing times)
        $establishmentStats = DB::table('bus_pass_applications')
            ->join('establishments', 'bus_pass_applications.establishment_id', '=', 'establishments.id')
            ->select(
                'establishments.name as establishment_name',
                DB::raw('count(*) as total_applications'),
                DB::raw('sum(case when status = "approved_for_integration" then 1 else 0 end) as approved'),
                DB::raw('sum(case when status = "rejected" then 1 else 0 end) as rejected'),
                DB::raw('sum(case when status in ("forwarded_to_movement", "pending_dmov_subject_clerk", "pending_dmov_staff_officer_2", "pending_dmov_staff_officer_1") then 1 else 0 end) as pending')
            )
            ->groupBy('establishments.id', 'establishments.name')
            ->get();

        $labels = [];
        $approved = [];
        $rejected = [];
        $pending = [];

        foreach ($establishmentStats as $stat) {
            $labels[] = $stat->establishment_name;
            $approved[] = $stat->approved;
            $rejected[] = $stat->rejected;
            $pending[] = $stat->pending;
        }

        return [
            'labels' => $labels,
            'approved' => $approved,
            'rejected' => $rejected,
            'pending' => $pending
        ];
    }
}
