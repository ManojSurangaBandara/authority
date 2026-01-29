<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SlcmpInchargeAssignment extends Model
{
    protected $table = 'slcmp_incharge_assignments';

    protected $fillable = [
        'bus_route_id',
        'route_id',
        'route_type',
        'living_in_bus_id',
        'slcmp_incharge_id',
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

    /**
     * Relationship with SlcmpIncharge
     */
    public function slcmpIncharge()
    {
        return $this->belongsTo(SlcmpIncharge::class, 'slcmp_incharge_id');
    }

    /**
     * Relationship with BusRoute (for living_out routes)
     */
    public function route()
    {
        return $this->belongsTo(BusRoute::class, 'route_id');
    }

    /**
     * Relationship with LivingInBuses (for living_in routes)
     */
    public function livingInBus()
    {
        return $this->belongsTo(LivingInBuses::class, 'living_in_bus_id');
    }

    /**
     * Get the route attribute dynamically based on route_type
     */
    public function getRouteAttribute()
    {
        if ($this->route_type === 'living_in' && $this->livingInBus) {
            return $this->livingInBus;
        } elseif ($this->route_type === 'living_out' && $this->busRoute) {
            return $this->busRoute;
        }
        return null;
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

    /**
     * Scope for active assignments
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
