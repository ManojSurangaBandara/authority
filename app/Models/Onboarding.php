<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Onboarding extends Model
{
    protected $fillable = [
        'bus_pass_application_id',
        'trip_id',
        'onboarded_at',
    ];

    protected $casts = [
        'onboarded_at' => 'datetime',
    ];

    /**
     * Relationship with BusPassApplication
     */
    public function busPassApplication(): BelongsTo
    {
        return $this->belongsTo(BusPassApplication::class);
    }

    /**
     * Relationship with Trip
     */
    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Get escort from trip
     */
    public function escort()
    {
        return $this->trip ? $this->trip->escort() : null;
    }

    /**
     * Get bus route from trip
     */
    public function busRoute()
    {
        return $this->trip ? $this->trip->busRoute() : null;
    }

    /**
     * Get living in bus from trip
     */
    public function livingInBus()
    {
        return $this->trip ? $this->trip->livingInBus() : null;
    }

    /**
     * Get route type from trip
     */
    public function getRouteTypeAttribute()
    {
        return $this->trip ? $this->trip->route_type : null;
    }

    /**
     * Get branch card id from bus pass application
     */
    public function getBranchCardIdAttribute()
    {
        return $this->busPassApplication ? $this->busPassApplication->branch_card_id : null;
    }

    /**
     * Get serial number from bus pass application
     */
    public function getSerialNumberAttribute()
    {
        return $this->busPassApplication ? $this->busPassApplication->serial_number : null;
    }

    /**
     * Get boarding data from bus pass application
     */
    public function getBoardingDataAttribute()
    {
        return $this->busPassApplication ? $this->busPassApplication->boarding_data : null;
    }

    /**
     * Get escort name
     */
    public function getEscortNameAttribute()
    {
        return $this->escort ? $this->escort->name : null;
    }

    /**
     * Get route name
     */
    public function getRouteNameAttribute()
    {
        if ($this->route_type === 'living_out') {
            return $this->busRoute ? $this->busRoute->name : null;
        } elseif ($this->route_type === 'living_in') {
            return $this->livingInBus ? $this->livingInBus->name : null;
        }
        return null;
    }
}
