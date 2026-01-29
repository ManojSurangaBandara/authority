<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BusEscortAssignment extends Model
{
    protected $table = 'bus_escort_assignments';

    protected $fillable = [
        'bus_route_id',
        'route_type',
        'living_in_bus_id',
        'escort_id',
        'status',
        'created_by'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $casts = [];

    public function busRoute()
    {
        return $this->belongsTo(BusRoute::class, 'bus_route_id');
    }

    public function livingInBus()
    {
        return $this->belongsTo(LivingInBuses::class, 'living_in_bus_id');
    }

    /**
     * Relationship with Escort
     */
    public function escort()
    {
        return $this->belongsTo(Escort::class, 'escort_id');
    }

    /**
     * Get the route name based on route type
     */
    public function getRouteNameAttribute()
    {
        if ($this->route_type === 'living_in' && $this->livingInBus) {
            return $this->livingInBus->name;
        } elseif ($this->route_type === 'living_out' && $this->busRoute) {
            return $this->busRoute->name;
        }
        return 'Unknown Route';
    }

    public function getStatusBadgeAttribute()
    {
        return $this->status === 'active'
            ? '<span class="badge badge-success">Active</span>'
            : '<span class="badge badge-secondary">Inactive</span>';
    }

    public function getFormattedAssignedDateAttribute()
    {
        return null;
    }

    public function getFormattedEndDateAttribute()
    {
        return null;
    }

    /**
     * Scope for active assignments
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
