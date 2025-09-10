<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusPassApplication extends Model
{
    protected $fillable = [
        'regiment_no',
        'rank',
        'name',
        'unit',
        'nic',
        'army_id',
        'permanent_address',
        'telephone_no',
        'grama_seva_division',
        'nearest_police_station',
        'branch_directorate',
        'marital_status',
        'approval_living_out',
        'obtain_sltb_season',
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
        'rent_allowance_order',
        'living_in_bus',
        'destination_location_ahq',
        'weekend_bus_name',
        'weekend_destination',
        'declaration_1',
        'declaration_2',
        'status',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'date_arrival_ahq' => 'date',
    ];

    // Relationship with BusPassStatus
    public function statusData()
    {
        return $this->belongsTo(BusPassStatus::class, 'status', 'code');
    }

    // Status label accessor
    public function getStatusLabelAttribute()
    {
        $statusData = $this->statusData;
        if ($statusData) {
            return $statusData->label;
        }

        // Fallback to hardcoded labels for backward compatibility
        $labels = [
            'pending_subject_clerk' => 'Pending - Subject Clerk Review',
            'pending_staff_officer_branch' => 'Pending - Staff Officer (Branch/Dte)',
            'pending_director_branch' => 'Pending - Director (Branch/Dte)',
            'forwarded_to_movement' => 'Forwarded to Movement',
            'pending_staff_officer_2_mov' => 'Pending - Staff Officer 2 (Movement)',
            'pending_staff_officer_1_mov' => 'Pending - Staff Officer 1 (Movement)',
            'pending_col_mov' => 'Pending - Colonel Movement',
            'pending_director_mov' => 'Pending - Director (Movement)',
            'approved_for_integration' => 'Approved for Branch Card Integration',
            'approved_for_temp_card' => 'Approved for Temporary Card',
            'integrated_to_branch_card' => 'Integrated to Branch Card',
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
        $statusData = $this->statusData;
        if ($statusData) {
            return $statusData->badge_html;
        }

        // Fallback badge colors
        $badges = [
            'pending_subject_clerk' => 'warning',
            'pending_staff_officer_branch' => 'info',
            'pending_director_branch' => 'primary',
            'forwarded_to_movement' => 'secondary',
            'pending_staff_officer_2_mov' => 'info',
            'pending_staff_officer_1_mov' => 'info',
            'pending_col_mov' => 'primary',
            'pending_director_mov' => 'primary',
            'approved_for_integration' => 'success',
            'approved_for_temp_card' => 'success',
            'integrated_to_branch_card' => 'success',
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
        return $this->bus_pass_type === 'daily_travel' ? 'Daily Travel' : 'Weekend/Monthly Travel';
    }

    // Get bus pass type label (legacy method for backward compatibility)
    public function getTypeLabel()
    {
        return $this->type_label;
    }
}
