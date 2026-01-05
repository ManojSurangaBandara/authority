<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BusDriverAssignment extends Model
{
    protected $fillable = [
        'bus_route_id',
        'route_id',
        'route_type',
        'living_in_bus_id',
        'driver_id',
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
     * Relationship with LivingInBuses
     */
    public function livingInBus()
    {
        return $this->belongsTo(LivingInBuses::class, 'living_in_bus_id');
    }

    /**
     * Get the route (either BusRoute or LivingInBuses)
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

    /**
     * Get route name
     */
    public function getRouteNameAttribute()
    {
        $route = $this->getRouteAttribute();
        return $route ? $route->name : null;
    }

    /**
     * Relationship with Driver
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
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
