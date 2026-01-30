<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $fillable = [
        'escort_id',
        'bus_route_id',
        'route_type',
        'driver_id',
        'bus_id',
        'slcmp_incharge_id',
        'start_latitude',
        'start_longitude',
        'trip_start_time',
        'end_latitude',
        'end_longitude',
        'trip_end_time'
    ];

    protected $casts = [
        'trip_start_time' => 'datetime',
        'trip_end_time' => 'datetime',
        'start_latitude' => 'float',
        'start_longitude' => 'float',
        'end_latitude' => 'float',
        'end_longitude' => 'float',
    ];

    // Relationships
    public function escort()
    {
        return $this->belongsTo(Escort::class, 'escort_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    public function slcmpIncharge()
    {
        return $this->belongsTo(SlcmpIncharge::class, 'slcmp_incharge_id');
    }

    public function busRoute()
    {
        if ($this->route_type === 'living_out') {
            return $this->belongsTo(BusRoute::class, 'bus_route_id');
        } else {
            return $this->belongsTo(LivingInBuses::class, 'bus_route_id');
        }
    }

    public function livingInBus()
    {
        return $this->belongsTo(LivingInBuses::class, 'bus_route_id');
    }

    /**
     * Relationship with Onboardings
     */
    public function onboardings()
    {
        return $this->hasMany(Onboarding::class);
    }
}
