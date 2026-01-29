<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $table = 'incidents';
    protected $fillable = [
        'incident_type_id',
        'trip_id',
        'description',
        'latitude',
        'longitude',
        'image1',
        'image2',
        'image3'
    ];

    public function incidentType()
    {
        return $this->belongsTo(IncidentType::class);
    }

    public function trip()
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
     * Get driver from trip
     */
    public function driver()
    {
        return $this->trip ? $this->trip->driver() : null;
    }

    /**
     * Get bus from trip
     */
    public function bus()
    {
        return $this->trip ? $this->trip->bus() : null;
    }

    /**
     * Get SLCMP incharge from trip
     */
    public function slcmpIncharge()
    {
        return $this->trip ? $this->trip->slcmpIncharge() : null;
    }

    /**
     * Get bus route from trip
     */
    public function busRoute()
    {
        return $this->trip ? $this->trip->busRoute() : null;
    }

    /**
     * Get route type from trip
     */
    public function getRouteTypeAttribute()
    {
        return $this->trip ? $this->trip->route_type : null;
    }

    /**
     * Get escort name
     */
    public function getEscortNameAttribute()
    {
        return $this->escort ? $this->escort->name : null;
    }

    /**
     * Get driver name
     */
    public function getDriverNameAttribute()
    {
        return $this->driver ? $this->driver->name : null;
    }

    /**
     * Get bus details
     */
    public function getBusDetailsAttribute()
    {
        return $this->bus ? [
            'id' => $this->bus->id,
            'name' => $this->bus->name,
            'no' => $this->bus->no
        ] : null;
    }

    /**
     * Get route details
     */
    public function getRouteDetailsAttribute()
    {
        return $this->busRoute ? [
            'id' => $this->busRoute->id,
            'name' => $this->busRoute->name,
            'type' => $this->route_type
        ] : null;
    }

    /**
     * Get SLCMP incharge name
     */
    public function getSlcmpInchargeNameAttribute()
    {
        return $this->slcmpIncharge ? $this->slcmpIncharge->name : null;
    }
}
