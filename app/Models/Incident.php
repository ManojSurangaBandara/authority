<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $table = 'incidents';
    protected $fillable = [
        'incident_type_id',
        'description',
        'latitude',
        'longitude',
        'image1',
        'image2',
        'image3',
        'escort_id',
        'bus_route_id',
        'route_type',
        'slcmp_incharge_id',
        'driver_id',
        'bus_id'
    ];

    public function incidentType()
    {
        return $this->belongsTo(IncidentType::class);
    }

    public function escort()
    {
        return $this->belongsTo(Escort::class);
    }

    public function busRoute()
    {
        if ($this->route_type === 'living_out') {
            return $this->belongsTo(BusRoute::class, 'bus_route_id');
        } elseif ($this->route_type === 'living_in') {
            return $this->belongsTo(LivingInBus::class, 'bus_route_id');
        }
        return null;
    }

    public function slcmpIncharge()
    {
        return $this->belongsTo(SlcmpIncharge::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }
}
