<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    protected $table = 'buses';

    protected $fillable = [
        'no',
        'name',
        'type_id',
        'no_of_seats',
        'total_capacity',
    ];

    public function type()
    {
        return $this->belongsTo(BusType::class, 'type_id');
    }

    public function routes()
    {
        return $this->hasMany(BusRoute::class, 'bus_id');
    }

    public function assignedRoute()
    {
        return $this->hasOne(BusRoute::class, 'bus_id');
    }

    public function fillingStationAssignment()
    {
        return $this->hasOne(BusFillingStationAssignment::class, 'bus_id')
            ->where('status', 'active');
    }

    public function fillingStationAssignments()
    {
        return $this->hasMany(BusFillingStationAssignment::class, 'bus_id');
    }

    public function routeAssignments()
    {
        return $this->hasMany(BusRouteAssignment::class, 'bus_id');
    }

    public function activeRouteAssignment()
    {
        return $this->hasOne(BusRouteAssignment::class, 'bus_id')->active();
    }
}
