<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteGroupMember extends Model
{
    protected $fillable = [
        'route_group_id',
        'route_type',
        'route_id',
    ];

    public function group()
    {
        return $this->belongsTo(RouteGroup::class, 'route_group_id');
    }

    public function route()
    {
        return $this->route_type === 'living_out'
            ? $this->belongsTo(BusRoute::class, 'route_id')
            : $this->belongsTo(LivingInBuses::class, 'route_id');
    }
}
