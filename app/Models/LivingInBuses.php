<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivingInBuses extends Model
{
    protected $table = 'living_in_buses';

    protected $fillable = [
        'name',
    ];

    public function routeAssignments()
    {
        return $this->hasMany(BusRouteAssignment::class, 'route_id')
            ->where('route_type', 'living_in');
    }

    public function activeRouteAssignment()
    {
        return $this->hasOne(BusRouteAssignment::class, 'route_id')
            ->where('route_type', 'living_in')
            ->where('status', 'active');
    }

    public function assignedBus()
    {
        return $this->hasOneThrough(
            Bus::class,
            BusRouteAssignment::class,
            'route_id', // Foreign key on BusRouteAssignment table
            'id', // Foreign key on Bus table
            'id', // Local key on LivingInBuses table
            'bus_id' // Local key on BusRouteAssignment table
        )->where('bus_route_assignments.route_type', 'living_in')
            ->where('bus_route_assignments.status', 'active');
    }
}
