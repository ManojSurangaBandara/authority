<?php

use Illuminate\Support\Facades\Auth;
use App\Models\BusPassApplication;
use App\Models\User;

if (!function_exists('getPendingApprovalsCount')) {
    function getPendingApprovalsCount()
    {
        if (!Auth::check()) {
            return 0;
        }

        /** @var User $user */
        $user = Auth::user();
        $pendingCount = 0;

        // Calculate pending approvals based on user role
        if ($user->hasRole('Bus Pass Subject Clerk (Branch)')) {
            $pendingCount = BusPassApplication::where('status', 'pending_subject_clerk')
                ->where('establishment_id', $user->establishment_id)
                ->count();
        } elseif ($user->hasRole('Staff Officer (Branch)')) {
            $pendingCount = BusPassApplication::whereIn('status', ['pending_staff_officer_branch', 'rejected_for_integration'])
                ->where('establishment_id', $user->establishment_id)
                ->count();
        } elseif ($user->hasRole('Subject Clerk (DMOV)')) {
            $pendingCount = BusPassApplication::where('status', 'forwarded_to_movement')->count();
        } elseif ($user->hasRole('Staff Officer 2 (DMOV)')) {
            $pendingCount = BusPassApplication::where('status', 'pending_staff_officer_2_mov')->count();
        } elseif ($user->hasRole('Col Mov (DMOV)') || $user->hasRole('Director (DMOV)')) {
            $pendingCount = BusPassApplication::where('status', 'pending_col_mov')->count();
        }

        return $pendingCount;
    }
}
