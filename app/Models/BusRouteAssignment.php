<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusRouteAssignment extends Model
{
    protected $fillable = [
        'bus_id',
        'route_id',
        'route_type',
        'status',
    ];

    protected $casts = [
        'route_type' => 'string',
        'status' => 'string',
    ];

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    public function getRouteAttribute()
    {
        if ($this->route_type === 'living_out') {
            return BusRoute::find($this->route_id);
        } elseif ($this->route_type === 'living_in') {
            return LivingInBuses::find($this->route_id);
        }
        return null;
    }

    public function getRouteNameAttribute()
    {
        $route = $this->getRouteAttribute();
        return $route ? $route->name : null;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeLivingOut($query)
    {
        return $query->where('route_type', 'living_out');
    }

    public function scopeLivingIn($query)
    {
        return $query->where('route_type', 'living_in');
    }
}
