<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Onboarding extends Model
{
    protected $fillable = [
        'bus_pass_application_id',
        'escort_id',
        'bus_route_id',
        'living_in_bus_id',
        'trip_id',
        'route_type',
        'branch_card_id',
        'serial_number',
        'onboarded_at',
        'boarding_data',
    ];

    protected $casts = [
        'onboarded_at' => 'datetime',
        'boarding_data' => 'array',
    ];

    /**
     * Relationship with BusPassApplication
     */
    public function busPassApplication(): BelongsTo
    {
        return $this->belongsTo(BusPassApplication::class);
    }

    /**
     * Relationship with Escort
     */
    public function escort(): BelongsTo
    {
        return $this->belongsTo(Escort::class);
    }

    /**
     * Relationship with Trip
     */
    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Relationship with BusRoute (for living out routes)
     */
    public function busRoute(): BelongsTo
    {
        return $this->belongsTo(BusRoute::class);
    }

    /**
     * Relationship with LivingInBus (for living in routes)
     */
    public function livingInBus(): BelongsTo
    {
        return $this->belongsTo(LivingInBuses::class);
    }
}
