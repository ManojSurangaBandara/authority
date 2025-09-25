<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = [
        'regiment_no',
        'rank',
        'name',
        'contact_no'
    ];

    public function driverAssignment()
    {
        return $this->hasOne(BusDriverAssignment::class, 'driver_id')
            ->where('status', 'active');
    }

    public function driverAssignments()
    {
        return $this->hasMany(BusDriverAssignment::class, 'driver_id');
    }
}
