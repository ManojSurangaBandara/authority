<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusRoute extends Model
{
    protected $table = 'bus_routes';

    protected $fillable = [
        'name',
        'bus_id',
    ];

    public function bus()
    {
        return $this->belongsTo(Bus::class, 'bus_id');
    }

    public function driverAssignment()
    {
        return $this->hasOne(BusDriverAssignment::class, 'route_id')
            ->where('route_type', 'living_out')
            ->where('status', 'active');
    }

    public function driverAssignments()
    {
        return $this->hasMany(BusDriverAssignment::class, 'route_id')
            ->where('route_type', 'living_out');
    }

    public function escortAssignment()
    {
        return $this->hasOne(BusEscortAssignment::class, 'bus_route_id')
            ->where('status', 'active');
    }

    public function escortAssignments()
    {
        return $this->hasMany(BusEscortAssignment::class, 'bus_route_id');
    }

    public function slcmpInchargeAssignment()
    {
        return $this->hasOne(SlcmpInchargeAssignment::class, 'route_id')
            ->where('route_type', 'living_out')
            ->where('status', 'active');
    }

    public function slcmpInchargeAssignments()
    {
        return $this->hasMany(SlcmpInchargeAssignment::class, 'route_id')
            ->where('route_type', 'living_out');
    }

    public function routeAssignments()
    {
        return $this->hasMany(BusRouteAssignment::class, 'route_id')
            ->where('route_type', 'living_out');
    }

    public function activeRouteAssignment()
    {
        return $this->hasOne(BusRouteAssignment::class, 'route_id')
            ->where('route_type', 'living_out')
            ->where('status', 'active');
    }

    public function assignedBus()
    {
        return $this->hasOneThrough(
            Bus::class,
            BusRouteAssignment::class,
            'route_id', // Foreign key on BusRouteAssignment table
            'id', // Foreign key on Bus table
            'id', // Local key on BusRoute table
            'bus_id' // Local key on BusRouteAssignment table
        )->where('bus_route_assignments.route_type', 'living_out')
            ->where('bus_route_assignments.status', 'active');
    }
}
