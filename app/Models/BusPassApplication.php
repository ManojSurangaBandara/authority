<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusPassApplication extends Model
{
    protected $fillable = [
        'person_id',
        'establishment_id',
        'branch_directorate',
        'marital_status',
        'approval_living_out',
        'obtain_sltb_season',
        'branch_card_availability',
        'branch_card_id',
        'date_arrival_ahq',
        'grama_niladari_certificate',
        'person_image',
        'bus_pass_type',
        // Daily travel fields
        'daily_route_from',
        'daily_route_to',
        'daily_start_date',
        'daily_end_date',
        'daily_reason',
        // Weekend/Monthly travel fields
        'weekend_route_from',
        'weekend_route_to',
        'weekend_frequency',
        'weekend_start_date',
        'weekend_end_date',
        'weekend_reason',
        // Other fields
        'requested_bus_name',
        'destination_from_ahq',
        'marriage_part_ii_order',
        'permission_letter',
        'living_in_bus',
        'destination_location_ahq',
        'weekend_bus_name',
        'weekend_destination',
        'declaration_1',
        'declaration_2',
        'status',
        'remarks',
        'created_by',
        'temp_card_qr',
    ];

    protected $casts = [
        'date_arrival_ahq' => 'date',
    ];

    // Relationship with BusPassStatus
    public function statusData()
    {
        return $this->belongsTo(BusPassStatus::class, 'status', 'code');
    }

    // Relationship with Person
    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    // Relationship with Establishment
    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    // Relationship with approval history
    public function approvalHistory()
    {
        return $this->hasMany(BusPassApprovalHistory::class)->ordered();
    }

    // Relationship with branch card switch history
    public function branchCardSwitchHistory()
    {
        return $this->hasMany(BranchCardSwitchHistory::class);
    }

    // Relationship with destination location
    public function destinationLocation()
    {
        return $this->belongsTo(DestinationLocation::class, 'destination_location_ahq', 'id');
    }

    // Status label accessor
    public function getStatusLabelAttribute()
    {
        // Check if current user is a branch user and status is in DMOV workflow
        $dmovStatuses = ['forwarded_to_movement', 'pending_staff_officer_2_mov', 'pending_col_mov'];
        if (auth()->check() && auth()->user()->isBranchUser() && in_array($this->status, $dmovStatuses)) {
            return 'Submitted';
        }

        $statusData = $this->statusData;
        if ($statusData) {
            return $statusData->label;
        }

        // Fallback to hardcoded labels for backward compatibility
        $labels = [
            'pending_subject_clerk' => 'Pending - Subject Clerk Review',
            'pending_staff_officer_branch' => 'Pending - Staff Officer (Branch/Dte)',
            'forwarded_to_movement' => 'Forwarded to Movement',
            'pending_staff_officer_2_mov' => 'Pending - Staff Officer 2 (Movement)',
            'pending_col_mov' => 'Pending - Colonel Movement',
            'approved_for_integration' => 'Approved for Branch Card Integration',
            'approved_for_temp_card' => 'Approved for Temporary Card',
            'integrated_to_branch_card' => 'Integrated to Branch Card',
            'rejected_for_integration' => 'Rejected for Integration',
            'temp_card_printed' => 'Temporary Card Printed',
            'temp_card_handed_over' => 'Temporary Card Handed Over',
            'rejected' => 'Rejected',
            'deactivated' => 'Deactivated',
            // Legacy statuses
            'pending' => 'Pending',
            'approved_by_staff' => 'Approved by Staff Officer',
            'approved_by_director' => 'Approved by Director',
            'approved' => 'Approved',
        ];

        return $labels[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    // Status badge accessor
    public function getStatusBadgeAttribute()
    {
        // Check if current user is a branch user
        if (auth()->check() && auth()->user()->isBranchUser()) {
            // Branch users should see real status for these statuses
            $allowedStatuses = [
                'pending_subject_clerk',
                'pending_staff_officer_branch',
                'integrated_to_branch_card',
                'integrated_to_temp_card',
                'temp_card_printed',
                'temp_card_handed_over',
                'rejected',
                'rejected_for_integration',
                'deactivated'
            ];
            if (!in_array($this->status, $allowedStatuses)) {
                return '<span class="badge badge-secondary">Submitted</span>';
            }
        }

        $statusData = $this->statusData;
        if ($statusData) {
            return $statusData->badge_html;
        }

        // Fallback badge colors
        $badges = [
            'pending_subject_clerk' => 'warning',
            'pending_staff_officer_branch' => 'info',
            'forwarded_to_movement' => 'secondary',
            'pending_staff_officer_2_mov' => 'info',
            'pending_col_mov' => 'primary',
            'approved_for_integration' => 'success',
            'approved_for_temp_card' => 'success',
            'integrated_to_branch_card' => 'success',
            'rejected_for_integration' => 'warning',
            'temp_card_printed' => 'success',
            'temp_card_handed_over' => 'success',
            'rejected' => 'danger',
            'deactivated' => 'dark',
            // Legacy statuses
            'pending' => 'warning',
            'approved_by_staff' => 'info',
            'approved_by_director' => 'primary',
            'approved' => 'success',
        ];

        $class = $badges[$this->status] ?? 'secondary';
        return '<span class="badge badge-' . $class . '">' . $this->status_label . '</span>';
    }

    // Get status label (legacy method for backward compatibility)
    public function getStatusLabel()
    {
        return $this->status_label;
    }

    // Bus pass type label accessor
    public function getTypeLabelAttribute()
    {
        $labels = [
            'daily_travel' => 'Daily Travel (Living out)',
            'weekend_monthly_travel' => 'Weekend and Living in Bus',
            'living_in_only' => 'Living in Bus only',
            'weekend_only' => 'Weekend only',
            'unmarried_daily_travel' => 'Unmarried Daily Travel'
        ];

        return $labels[$this->bus_pass_type] ?? ucfirst(str_replace('_', ' ', $this->bus_pass_type));
    }

    // Get bus pass type label (legacy method for backward compatibility)
    public function getTypeLabel()
    {
        return $this->type_label;
    }

    // Get rejection information
    public function getRejectedByAttribute()
    {
        $rejectionHistory = $this->approvalHistory()
            ->where('action', 'rejected')
            ->with('user')
            ->orderBy('action_date', 'desc')
            ->first();

        if ($rejectionHistory && $rejectionHistory->user) {
            return $rejectionHistory->user->name;
        }

        return null;
    }

    // Get rejection reason
    public function getRejectionReasonAttribute()
    {
        $rejectionHistory = $this->approvalHistory()
            ->where('action', 'rejected')
            ->orderBy('action_date', 'desc')
            ->first();

        if ($rejectionHistory) {
            return $rejectionHistory->remarks;
        }

        return null;
    }

    // Get rejection date
    public function getRejectedAtAttribute()
    {
        $rejectionHistory = $this->approvalHistory()
            ->where('action', 'rejected')
            ->orderBy('action_date', 'desc')
            ->first();

        if ($rejectionHistory) {
            return $rejectionHistory->action_date;
        }

        return null;
    }

    /**
     * Get the latest "not recommended" action for this application
     */
    public function getLatestNotRecommendedAction()
    {
        return $this->approvalHistory()
            ->where('action', 'not_recommended')
            ->with('user')
            ->orderBy('action_date', 'desc')
            ->first();
    }

    /**
     * Check if application was recently not recommended
     */
    public function wasRecentlyNotRecommended()
    {
        $latestNotRecommended = $this->getLatestNotRecommendedAction();

        if (!$latestNotRecommended) {
            return false;
        }

        // Check if the latest not recommended action is more recent than any approved/forwarded actions
        $latestApprovalAfter = $this->approvalHistory()
            ->whereIn('action', ['approved', 'forwarded', 'recommended'])
            ->where('action_date', '>', $latestNotRecommended->action_date)
            ->exists();

        return !$latestApprovalAfter;
    }

    /**
     * Get latest DMOV not recommended action
     */
    public function getLatestDmovNotRecommendedAction()
    {
        return $this->approvalHistory()
            ->where('action', 'dmov_not_recommended')
            ->with('user')
            ->orderBy('action_date', 'desc')
            ->first();
    }

    /**
     * Check if application was recently not recommended by DMOV
     */
    public function wasRecentlyDmovNotRecommended()
    {
        $latestDmovNotRecommended = $this->getLatestDmovNotRecommendedAction();

        if (!$latestDmovNotRecommended) {
            return false;
        }

        // Check if the latest DMOV not recommended action is more recent than any approved/forwarded actions
        $latestApprovalAfter = $this->approvalHistory()
            ->whereIn('action', ['approved', 'forwarded', 'recommended'])
            ->where('action_date', '>', $latestDmovNotRecommended->action_date)
            ->exists();

        return !$latestApprovalAfter;
    }

    /**
     * Get latest integration rejected action
     */
    public function getLatestIntegrationRejectedAction()
    {
        return $this->approvalHistory()
            ->where('action', 'integration_rejected')
            ->with('user')
            ->orderBy('action_date', 'desc')
            ->first();
    }

    /**
     * Check if application was recently rejected from integration
     */
    public function wasRecentlyRejectedFromIntegration()
    {
        $latestIntegrationRejected = $this->getLatestIntegrationRejectedAction();

        if (!$latestIntegrationRejected) {
            return false;
        }

        // Check if the latest integration rejected action is more recent than any approved/forwarded actions
        $latestApprovalAfter = $this->approvalHistory()
            ->whereIn('action', ['approved', 'forwarded', 'recommended'])
            ->where('action_date', '>', $latestIntegrationRejected->action_date)
            ->exists();

        return !$latestApprovalAfter;
    }

    /**
     * Get approved bus pass count for a specific route
     */
    public function getApprovedCountForRoute($routeName, $routeType = 'living_out')
    {
        if (!$routeName) {
            return 0;
        }

        $query = self::whereIn('status', ['approved', 'approved_for_integration', 'approved_for_temp_card']);

        if ($routeType === 'living_out') {
            $query->where('requested_bus_name', $routeName);
        } elseif ($routeType === 'living_in') {
            $query->where('living_in_bus', $routeName);
        } elseif ($routeType === 'weekend') {
            $query->where('weekend_bus_name', $routeName);
        }

        return $query->count();
    }

    /**
     * Get approved count for route by specific roles
     */
    public function getApprovedCountByRolesForRoute($roles, $routeName, $routeType = 'living_out')
    {
        if (!$routeName) {
            return 0;
        }

        $query = self::whereIn('status', ['approved', 'approved_for_integration', 'approved_for_temp_card'])
            ->whereHas('approvalHistory', function ($q) use ($roles) {
                $q->where('action', 'approved')
                    ->whereHas('user', function ($uq) use ($roles) {
                        $uq->whereHas('roles', function ($rq) use ($roles) {
                            $rq->whereIn('name', $roles);
                        });
                    });
            });

        if ($routeType === 'living_out') {
            $query->where('requested_bus_name', $routeName);
        } elseif ($routeType === 'living_in') {
            $query->where('living_in_bus', $routeName);
        } elseif ($routeType === 'weekend') {
            $query->where('weekend_bus_name', $routeName);
        }

        return $query->count();
    }

    /**
     * Get pending bus pass count for a specific route
     */
    public function getPendingCountForRoute($routeName, $routeType = 'living_out')
    {
        if (!$routeName) {
            return 0;
        }

        $query = self::whereIn('status', [
            'pending_subject_clerk',
            'pending_staff_officer_branch',
            'forwarded_to_movement',
            'pending_staff_officer_2_mov',
            'pending_col_mov',
            'pending_director_branch',
            'pending_director_dmov',
            'not_recommended',
            'dmov_not_recommended'
        ]);

        if ($routeType === 'living_out') {
            $query->where('requested_bus_name', $routeName);
        } elseif ($routeType === 'living_in') {
            $query->where('living_in_bus', $routeName);
        } elseif ($routeType === 'weekend') {
            $query->where('weekend_bus_name', $routeName);
        }

        return $query->count();
    }

    /**
     * Get pending bus pass count for a specific route by status
     */
    public function getPendingCountForRouteByStatus($status, $routeName, $routeType = 'living_out')
    {
        if (!$routeName) {
            return 0;
        }

        $query = self::where('status', $status);

        if ($routeType === 'living_out') {
            $query->where('requested_bus_name', $routeName);
        } elseif ($routeType === 'living_in') {
            $query->where('living_in_bus', $routeName);
        } elseif ($routeType === 'weekend') {
            $query->where('weekend_bus_name', $routeName);
        }

        return $query->count();
    }

    /**
     * Get pending bus pass count for a specific route by multiple statuses
     */
    public function getPendingCountForRouteByStatuses($statuses, $routeName, $routeType = 'living_out')
    {
        if (!$routeName || empty($statuses)) {
            return 0;
        }

        $query = self::whereIn('status', $statuses);

        if ($routeType === 'living_out') {
            $query->where('requested_bus_name', $routeName);
        } elseif ($routeType === 'living_in') {
            $query->where('living_in_bus', $routeName);
        } elseif ($routeType === 'weekend') {
            $query->where('weekend_bus_name', $routeName);
        }

        return $query->count();
    }

    /**
     * Get integrated bus pass count for a specific route
     */
    public function getIntegratedCountForRoute($routeName, $routeType = 'living_out')
    {
        if (!$routeName) {
            return 0;
        }

        $query = self::whereIn('status', ['integrated_to_branch_card', 'integrated_to_temp_card']);

        if ($routeType === 'living_out') {
            $query->where('requested_bus_name', $routeName);
        } elseif ($routeType === 'living_in') {
            $query->where('living_in_bus', $routeName);
        } elseif ($routeType === 'weekend') {
            $query->where('weekend_bus_name', $routeName);
        }

        return $query->count();
    }

    /**
     * Get seating capacity for a route based on assigned bus
     */
    public function getSeatingCapacityForRoute($routeName, $routeType = 'living_out')
    {
        if (!$routeName) {
            return null;
        }

        // Find the route in bus_route_assignments
        if ($routeType === 'living_out') {
            // Find route ID from bus_routes table
            $route = \App\Models\BusRoute::where('name', $routeName)->first();
            if ($route) {
                $assignment = \App\Models\BusRouteAssignment::active()
                    ->where('route_id', $route->id)
                    ->where('route_type', 'living_out')
                    ->with('bus')
                    ->first();
            }
        } elseif ($routeType === 'living_in') {
            // Find route ID from living_in_buses table
            $route = \App\Models\LivingInBuses::where('name', $routeName)->first();
            if ($route) {
                $assignment = \App\Models\BusRouteAssignment::active()
                    ->where('route_id', $route->id)
                    ->where('route_type', 'living_in')
                    ->with('bus')
                    ->first();
            }
        }

        if (isset($assignment) && $assignment && $assignment->bus) {
            return [
                'seats' => $assignment->bus->no_of_seats,
                'total_capacity' => $assignment->bus->total_capacity,
                'bus_name' => $assignment->bus->name,
                'bus_no' => $assignment->bus->no
            ];
        }

        return null;
    }

    /**
     * Check if the route has been updated during approval process
     */
    public function hasRouteBeenUpdated()
    {
        // Check for explicit route_updated actions (from integrations page)
        if ($this->approvalHistory()->where('action', 'route_updated')->exists()) {
            return true;
        }

        // Check for route changes in approval remarks (from approval process by level 4 & 5 users)
        $approvalHistory = $this->approvalHistory()
            ->whereIn('action', ['approved', 'forwarded'])
            ->where('remarks', 'like', '%--- System Updates ---%')
            ->get();

        foreach ($approvalHistory as $history) {
            $remarks = $history->remarks;

            // Check if remarks contain route-related updates
            $routeFields = ['Requested Bus Name', 'Living In Bus', 'Weekend Bus Name'];
            foreach ($routeFields as $field) {
                if (strpos($remarks, $field . ' updated from') !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get route statistics (approved count + pending count + seating capacity)
     */
    public function getRouteStatistics($routeName, $routeType = 'living_out', $approvedByRoles = null, $pendingStatus = null)
    {
        $approvedCount = $approvedByRoles
            ? $this->getApprovedCountByRolesForRoute($approvedByRoles, $routeName, $routeType)
            : $this->getApprovedCountForRoute($routeName, $routeType);

        $pendingCount = $pendingStatus
            ? $this->getPendingCountForRouteByStatus($pendingStatus, $routeName, $routeType)
            : $this->getPendingCountForRoute($routeName, $routeType);

        return [
            'all_pending_count' => $this->getPendingCountForRoute($routeName, $routeType),
            'integrated_count' => $this->getIntegratedCountForRoute($routeName, $routeType),
            'approved_count' => $approvedCount,
            'pending_count' => $pendingCount,
            'capacity_info' => $this->getSeatingCapacityForRoute($routeName, $routeType)
        ];
    }
}
