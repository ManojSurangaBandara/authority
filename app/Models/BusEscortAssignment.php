<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BusEscortAssignment extends Model
{
    protected $table = 'bus_escort_assignments';

    protected $fillable = [
        'bus_route_id',
        'escort_regiment_no',
        'escort_rank',
        'escort_name',
        'escort_contact_no',
        'assigned_date',
        'end_date',
        'status',
        'created_by'
    ];

    protected $dates = [
        'assigned_date',
        'end_date',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'end_date' => 'date',
    ];

    public function busRoute()
    {
        return $this->belongsTo(BusRoute::class, 'bus_route_id');
    }

    public function getStatusBadgeAttribute()
    {
        return $this->status === 'active' 
            ? '<span class="badge badge-success">Active</span>'
            : '<span class="badge badge-secondary">Inactive</span>';
    }

    public function getFormattedAssignedDateAttribute()
    {
        return $this->assigned_date ? $this->assigned_date->format('Y-m-d') : null;
    }

    public function getFormattedEndDateAttribute()
    {
        return $this->end_date ? $this->end_date->format('Y-m-d') : null;
    }
}
