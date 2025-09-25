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
}
