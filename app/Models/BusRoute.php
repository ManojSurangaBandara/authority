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
        return $this->hasOne(BusDriverAssignment::class, 'bus_route_id')
            ->where('status', 'active');
    }

    public function driverAssignments()
    {
        return $this->hasMany(BusDriverAssignment::class, 'bus_route_id');
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
        return $this->hasOne(SlcmpInchargeAssignment::class, 'bus_route_id')
            ->where('status', 'active');
    }

    public function slcmpInchargeAssignments()
    {
        return $this->hasMany(SlcmpInchargeAssignment::class, 'bus_route_id');
    }
}
