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
        $validationRules = [
            'remarks' => 'nullable|string|max:500'
        ];

        // Add SLTB season validation for Subject Clerk (DMOV)
        if (Auth::user()->hasRole('Subject Clerk (DMOV)')) {
            $validationRules['obtain_sltb_season'] = 'required|in:yes,no';
        }

        // Add bus name modification validation for SO2 DMOV
        if (Auth::user()->hasRole('Staff Officer 2 (DMOV)')) {
            $validationRules['requested_bus_name'] = 'nullable|string|max:255';
            $validationRules['living_in_bus'] = 'nullable|string|max:255';
            $validationRules['weekend_bus_name'] = 'nullable|string|max:255';
        }

        // Add SLTB season confirmation validation for higher level approvers when SLTB season is available
        if (!Auth::user()->hasRole(['Bus Pass Subject Clerk (Branch)', 'Subject Clerk (DMOV)']) && $application->obtain_sltb_season == 'yes') {
            $validationRules['sltb_season_confirmation'] = 'required|accepted';
        }

        $customMessages = [
            'sltb_season_confirmation.required' => 'You must confirm awareness of SLTB season availability before approving.',
            'sltb_season_confirmation.accepted' => 'You must check the SLTB season confirmation checkbox to proceed.'
        ];

        $request->validate($validationRules, $customMessages);

        $user = Auth::user();

        // Check if user has permission to approve this application
        if (!$this->canUserApprove($user, $application)) {
            return redirect()->back()->with('error', 'You do not have permission to approve this application.');
        }

        DB::transaction(function () use ($application, $user, $request) {
            // Determine action type based on user role
            $action = 'approved';
            if ($user->hasRole(['Bus Pass Subject Clerk (Branch)', 'Subject Clerk (DMOV)'])) {
                $action = 'forwarded';
            }

            // Prepare remarks with SLTB season and route update info if applicable
            $remarks = $request->remarks;
            $updateNotes = [];

            if ($user->hasRole('Subject Clerk (DMOV)')) {
                // SLTB Season update
                if ($request->has('obtain_sltb_season')) {
                    $oldValue = $application->obtain_sltb_season;
                    $newValue = $request->obtain_sltb_season;

                    $oldLabel = $oldValue == 'yes' ? 'Available' : ($oldValue == 'no' ? 'Not Available' : 'Not Set');
                    $newLabel = $newValue == 'yes' ? 'Available' : 'Not Available';

                    if ($oldValue !== $newValue) {
                        $updateNotes[] = "SLTB Season updated from '{$oldLabel}' to '{$newLabel}'";
                    }
                }
            }

            // Bus name modification updates for SO2 DMOV
            if ($user->hasRole('Staff Officer 2 (DMOV)')) {
                $busFields = [
                    'requested_bus_name' => 'Requested Bus Name',
                    'living_in_bus' => 'Living In Bus',
                    'weekend_bus_name' => 'Weekend Bus Name'
                ];

                foreach ($busFields as $field => $label) {
                    if ($request->has($field) && $request->filled($field)) {
                        $oldValue = $application->$field ?: 'Not Set';
                        $newValue = $request->$field;

                        if ($oldValue !== $newValue) {
                            $updateNotes[] = "{$label} updated from '{$oldValue}' to '{$newValue}'";
                        }
                    }
                }
            }

            // Combine update notes with remarks (separate lines with clear formatting)
            if (!empty($updateNotes)) {
                $updateText = implode("\n", $updateNotes);

                if ($remarks) {
                    // Manual remarks first, then system-generated updates on separate lines
                    $remarks = $remarks . "\n\n--- System Updates ---\n" . $updateText;
                } else {
                    // Only system-generated updates
                    $remarks = "--- System Updates ---\n" . $updateText;
                }
            }

            // Record action in history
            $this->recordApprovalAction($application, $user, $action, $remarks);

            // Update application status based on workflow
            $newStatus = $this->getNextApprovalStatus($application->status, $action);

            // Prepare update data
            $updateData = [
                'status' => $newStatus,
                'remarks' => $request->remarks
            ];

            // Update SLTB season if provided by Subject Clerk (DMOV)
            if ($user->hasRole('Subject Clerk (DMOV)')) {
                if ($request->has('obtain_sltb_season')) {
                    $updateData['obtain_sltb_season'] = $request->obtain_sltb_season;
                }
            }

            // Update bus names if provided by SO2 DMOV
            if ($user->hasRole('Staff Officer 2 (DMOV)')) {
                $busFields = ['requested_bus_name', 'living_in_bus', 'weekend_bus_name'];

                foreach ($busFields as $field) {
                    if ($request->has($field) && $request->filled($field)) {
                        $updateData[$field] = $request->$field;
                    }
                }
            }

            $application->update($updateData);
        });

        // Customize success message based on action
        $message = $user->hasRole(['Bus Pass Subject Clerk (Branch)', 'Subject Clerk (DMOV)'])
            ? 'Application forwarded successfully.'
            : 'Application approved successfully.';

        // Add bus name modification info to success message for SO2 DMOV
        if ($user->hasRole('Staff Officer 2 (DMOV)')) {
            $busUpdated = false;
            $busFields = ['requested_bus_name', 'living_in_bus', 'weekend_bus_name'];

            foreach ($busFields as $field) {
                if ($request->has($field) && $request->filled($field)) {
                    $busUpdated = true;
                    break;
                }
            }

            if ($busUpdated) {
                $message .= ' Bus information has been updated as requested.';
            }
        }

        return redirect()->back()->with('success', $message);
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
        $validationRules = [
            'remarks' => 'nullable|string|max:500'
        ];

        // Add SLTB season awareness validation if SLTB season is available
        if ($application->obtain_sltb_season == 'yes') {
            $validationRules['sltb_season_awareness'] = 'required|accepted';
        }

        $customMessages = [
            'sltb_season_awareness.required' => 'You must acknowledge awareness of SLTB season availability before recommending.',
            'sltb_season_awareness.accepted' => 'You must check the SLTB season awareness checkbox to proceed.'
        ];

        $request->validate($validationRules, $customMessages);

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

        return redirect()->back()->with('success', 'Application recommended successfully and forwarded to Movement.');
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
     * DMOV not recommend a bus pass application (Subject Clerk DMOV only)
     */
    public function dmovNotRecommend(Request $request, BusPassApplication $application)
    {
        $request->validate([
            'remarks' => 'required|string|max:500'
        ]);

        $user = Auth::user();

        // Check if user is Subject Clerk (DMOV)
        if (!$user->hasRole('Subject Clerk (DMOV)')) {
            return redirect()->back()->with('error', 'You do not have permission to process this application.');
        }

        // Check if user can process this application
        if (!$this->canUserApprove($user, $application)) {
            return redirect()->back()->with('error', 'You do not have permission to process this application.');
        }

        DB::transaction(function () use ($application, $user, $request) {
            // Record DMOV not recommendation in history
            $this->recordApprovalAction($application, $user, 'dmov_not_recommended', $request->remarks);

            // Set application status back to branch staff officer for review
            $application->update([
                'status' => 'pending_staff_officer_branch',
                'remarks' => $request->remarks
            ]);
        });

        return redirect()->back()->with('success', 'Application not recommended and returned to Branch Staff Officer for review.');
    }

    /**
     * Forward application back to Branch Clerk (Staff Officer Branch only for DMOV returned applications)
     */
    public function forwardToBranchClerk(Request $request, BusPassApplication $application)
    {
        $request->validate([
            'remarks' => 'required|string|max:500'
        ]);

        $user = Auth::user();

        // Check if user is Staff Officer (Branch)
        if (!$user->hasRole('Staff Officer (Branch)')) {
            return redirect()->back()->with('error', 'You do not have permission to process this application.');
        }

        // Check if application was recently returned from DMOV
        if (!$application->wasRecentlyDmovNotRecommended()) {
            return redirect()->back()->with('error', 'This action is only available for applications returned from DMOV.');
        }

        DB::transaction(function () use ($application, $user, $request) {
            // Record the forward action in history
            $this->recordApprovalAction($application, $user, 'forwarded_to_branch_clerk', $request->remarks);

            // Set application status back to branch subject clerk
            $application->update([
                'status' => 'pending_subject_clerk',
                'remarks' => $request->remarks
            ]);
        });

        return redirect()->back()->with('success', 'Application forwarded back to Branch Clerk for review.');
    }

    /**
     * Get pending applications for a specific user based on their role
     */
    private function getPendingApplicationsForUser($user)
    {
        $statuses = $this->getPendingStatusesForUserRole($user);

        if (empty($statuses)) {
            return collect();
        }

        $query = BusPassApplication::whereIn('status', $statuses)
            ->with(['person.gsDivision', 'person.policeStation', 'statusData', 'establishment', 'approvalHistory.user']);

        // Filter by establishment for branch roles
        if ($user->isBranchUser()) {
            $query->where('establishment_id', $user->establishment_id);
        }
        // Movement roles see all applications that reach their level
        // (no establishment filtering needed for DMOV roles)

        return $query->orderBy('created_at', 'asc')->get();
    }

    /**
     * Get the pending statuses that a user role should handle
     */
    private function getPendingStatusesForUserRole($user)
    {
        if ($user->hasRole('Bus Pass Subject Clerk (Branch)')) {
            return ['pending_subject_clerk'];
        }

        if ($user->hasRole('Staff Officer (Branch)')) {
            return [
                'pending_staff_officer_branch',
                'integrated_to_branch_card',
                'integrated_to_temp_card',
                'temp_card_printed',
                'temp_card_handed_over',
                'rejected',
                'deactivated'
            ];
        }

        if ($user->hasRole('Subject Clerk (DMOV)')) {
            return ['forwarded_to_movement'];
        }

        if ($user->hasRole('Staff Officer 2 (DMOV)')) {
            return ['pending_staff_officer_2_mov'];
        }

        if ($user->hasRole('Col Mov (DMOV)')) {
            return ['pending_col_mov'];
        }

        if ($user->hasRole('Director (DMOV)')) {
            return ['pending_col_mov'];
        }

        return [];
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

        // Treat 'forwarded' the same as 'approved' for status progression
        if ($action === 'forwarded') {
            $action = 'approved';
        }

        // Branch workflow
        if ($currentStatus === 'pending_subject_clerk') {
            return 'pending_staff_officer_branch';
        }

        if ($currentStatus === 'pending_staff_officer_branch') {
            return 'forwarded_to_movement';
        }

        // DMOV workflow
        if ($currentStatus === 'forwarded_to_movement') {
            return 'pending_staff_officer_2_mov';
        }

        if ($currentStatus === 'pending_staff_officer_2_mov') {
            return 'pending_col_mov';
        }

        if ($currentStatus === 'pending_col_mov') {
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
        } elseif ($action === 'dmov_not_recommended') {
            $newStatus = 'pending_staff_officer_branch';
        } elseif ($action === 'forwarded_to_branch_clerk') {
            $newStatus = 'pending_subject_clerk';
        } elseif ($action === 'recommended' || $action === 'approved' || $action === 'forwarded') {
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
