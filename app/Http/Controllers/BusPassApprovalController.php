<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BusPassApplication;
use App\Models\BusPassApprovalHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BusPassApprovalController extends Controller
{
    /**
     * Display pending applications for the current user's role
     */
    public function index()
    {
        $user = Auth::user();
        $pendingApplications = $this->getPendingApplicationsForUser($user);

        return view('bus-pass-approvals.index', compact('pendingApplications'));
    }

    /**
     * Approve a bus pass application
     */
    public function approve(Request $request, BusPassApplication $application)
    {
        $request->validate([
            'remarks' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();

        // Check if user has permission to approve this application
        if (!$this->canUserApprove($user, $application)) {
            return redirect()->back()->with('error', 'You do not have permission to approve this application.');
        }

        DB::transaction(function () use ($application, $user, $request) {
            // Record approval in history
            $this->recordApprovalAction($application, $user, 'approved', $request->remarks);

            // Update application status based on workflow
            $newStatus = $this->getNextApprovalStatus($application->status, 'approved');
            $application->update([
                'status' => $newStatus,
                'remarks' => $request->remarks
            ]);
        });

        return redirect()->back()->with('success', 'Application approved successfully.');
    }

    /**
     * Reject a bus pass application
     */
    public function reject(Request $request, BusPassApplication $application)
    {
        $request->validate([
            'remarks' => 'required|string|max:500'
        ]);

        $user = Auth::user();

        // Check if user has permission to reject this application
        if (!$this->canUserApprove($user, $application)) {
            return redirect()->back()->with('error', 'You do not have permission to reject this application.');
        }

        DB::transaction(function () use ($application, $user, $request) {
            // Record rejection in history
            $this->recordApprovalAction($application, $user, 'rejected', $request->remarks);

            // Set application status to rejected
            $application->update([
                'status' => 'rejected',
                'remarks' => $request->remarks
            ]);
        });

        return redirect()->back()->with('success', 'Application rejected successfully.');
    }

    /**
     * Recommend a bus pass application (Staff Officer Branch only)
     */
    public function recommend(Request $request, BusPassApplication $application)
    {
        $request->validate([
            'remarks' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();

        // Check if user is Staff Officer (Branch)
        if (!$user->hasRole('Staff Officer (Branch)')) {
            return redirect()->back()->with('error', 'You do not have permission to recommend this application.');
        }

        // Check if user can process this application
        if (!$this->canUserApprove($user, $application)) {
            return redirect()->back()->with('error', 'You do not have permission to process this application.');
        }

        DB::transaction(function () use ($application, $user, $request) {
            // Record recommendation in history
            $this->recordApprovalAction($application, $user, 'recommended', $request->remarks);

            // Update application status to next level
            $newStatus = $this->getNextApprovalStatus($application->status, 'approved');
            $application->update([
                'status' => $newStatus,
                'remarks' => $request->remarks
            ]);
        });

        return redirect()->back()->with('success', 'Application recommended successfully and forwarded to Director (Branch).');
    }

    /**
     * Not recommend a bus pass application (Staff Officer Branch only)
     */
    public function notRecommend(Request $request, BusPassApplication $application)
    {
        $request->validate([
            'remarks' => 'required|string|max:500'
        ]);

        $user = Auth::user();

        // Check if user is Staff Officer (Branch)
        if (!$user->hasRole('Staff Officer (Branch)')) {
            return redirect()->back()->with('error', 'You do not have permission to process this application.');
        }

        // Check if user can process this application
        if (!$this->canUserApprove($user, $application)) {
            return redirect()->back()->with('error', 'You do not have permission to process this application.');
        }

        DB::transaction(function () use ($application, $user, $request) {
            // Record not recommendation in history
            $this->recordApprovalAction($application, $user, 'not_recommended', $request->remarks);

            // Set application status back to subject clerk for review
            $application->update([
                'status' => 'pending_subject_clerk',
                'remarks' => $request->remarks
            ]);
        });

        return redirect()->back()->with('success', 'Application not recommended and returned to Subject Clerk for review.');
    }

    /**
     * Get pending applications for a specific user based on their role
     */
    private function getPendingApplicationsForUser($user)
    {
        $status = $this->getPendingStatusForUserRole($user);

        if (!$status) {
            return collect();
        }

        $query = BusPassApplication::where('status', $status)
            ->with(['person.rank', 'statusData', 'establishment', 'approvalHistory.user']);

        // Filter by establishment for branch roles
        if ($user->isBranchUser()) {
            $query->where('establishment_id', $user->establishment_id);
        }
        // Movement roles see all applications that reach their level
        // (no establishment filtering needed for DMOV roles)

        return $query->orderBy('created_at', 'asc')->get();
    }

    /**
     * Get the pending status that a user role should handle
     */
    private function getPendingStatusForUserRole($user)
    {
        if ($user->hasRole('Bus Pass Subject Clerk (Branch)')) {
            return 'pending_subject_clerk';
        }

        if ($user->hasRole('Staff Officer (Branch)')) {
            return 'pending_staff_officer_branch';
        }

        if ($user->hasRole('Director (Branch)')) {
            return 'pending_director_branch';
        }

        if ($user->hasRole('Subject Clerk (DMOV)')) {
            return 'forwarded_to_movement';
        }

        if ($user->hasRole('Staff Officer 2 (DMOV)')) {
            return 'pending_staff_officer_2_mov';
        }

        if ($user->hasRole('Staff Officer 1 (DMOV)')) {
            return 'pending_staff_officer_1_mov';
        }

        if ($user->hasRole('Col Mov (DMOV)')) {
            return 'pending_col_mov';
        }

        if ($user->hasRole('Director (DMOV)')) {
            return 'pending_director_mov';
        }

        return null;
    }

    /**
     * Check if user can approve a specific application
     */
    private function canUserApprove($user, $application)
    {
        $userPendingStatus = $this->getPendingStatusForUserRole($user);

        // Check if user's role matches the application status
        if ($userPendingStatus !== $application->status) {
            return false;
        }

        // For branch users, check if they belong to the same establishment
        if ($user->isBranchUser()) {
            return $user->establishment_id === $application->establishment_id;
        }

        // Movement users can approve any application that reaches their level
        return true;
    }

    /**
     * Get the next status in the approval workflow
     */
    private function getNextApprovalStatus($currentStatus, $action)
    {
        if ($action === 'rejected') {
            return 'rejected';
        }

        // Branch workflow
        if ($currentStatus === 'pending_subject_clerk') {
            return 'pending_staff_officer_branch';
        }

        if ($currentStatus === 'pending_staff_officer_branch') {
            return 'pending_director_branch';
        }

        if ($currentStatus === 'pending_director_branch') {
            return 'forwarded_to_movement';
        }

        // DMOV workflow
        if ($currentStatus === 'forwarded_to_movement') {
            return 'pending_staff_officer_2_mov';
        }

        if ($currentStatus === 'pending_staff_officer_2_mov') {
            return 'pending_staff_officer_1_mov';
        }

        if ($currentStatus === 'pending_staff_officer_1_mov') {
            return 'pending_col_mov';
        }

        if ($currentStatus === 'pending_col_mov') {
            return 'pending_director_mov';
        }

        if ($currentStatus === 'pending_director_mov') {
            return 'approved_for_integration';
        }

        return $currentStatus;
    }

    /**
     * Record approval action in history
     */
    private function recordApprovalAction($application, $user, $action, $remarks)
    {
        $newStatus = $application->status; // Default to current status

        if ($action === 'rejected') {
            $newStatus = 'rejected';
        } elseif ($action === 'not_recommended') {
            $newStatus = 'pending_subject_clerk';
        } elseif ($action === 'recommended' || $action === 'approved') {
            $newStatus = $this->getNextApprovalStatus($application->status, $action);
        }

        BusPassApprovalHistory::create([
            'bus_pass_application_id' => $application->id,
            'user_id' => $user->id,
            'action' => $action,
            'previous_status' => $application->status,
            'new_status' => $newStatus,
            'remarks' => $remarks,
            'action_date' => now()
        ]);
    }
}
