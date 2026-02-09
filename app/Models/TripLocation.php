<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripLocation extends Model
{
    protected $fillable = [
        'trip_id',
        'latitude',
        'longitude',
        'recorded_at',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'recorded_at' => 'datetime',
    ];

    /**
     * Relationship with Trip
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}
