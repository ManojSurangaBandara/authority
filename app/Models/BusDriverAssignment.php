<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BusDriverAssignment extends Model
{
    protected $fillable = [
        'bus_route_id',
        'driver_regiment_no',
        'driver_rank',
        'driver_name',
        'driver_contact_no',
        'assigned_date',
        'end_date',
        'status',
        'created_by'
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Relationship with BusRoute
     */
    public function busRoute()
    {
        return $this->belongsTo(BusRoute::class, 'bus_route_id');
    }

    /**
     * Get status badge for display
     */
    public function getStatusBadgeAttribute()
    {
        $colors = [
            'active' => 'success',
            'inactive' => 'secondary',
        ];

        $color = $colors[$this->status] ?? 'secondary';
        return '<span class="badge badge-' . $color . '">' . ucfirst($this->status) . '</span>';
    }

    /**
     * Scope for active assignments
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for inactive assignments
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}
