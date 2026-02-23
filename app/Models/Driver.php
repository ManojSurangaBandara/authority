<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = [
        'driver_type',
        'regiment_no',
        'nic',
        'rank',
        'name',
        'contact_no'
    ];

    public function driverAssignment()
    {
        return $this->hasOne(BusDriverAssignment::class, 'driver_id')
            ->where('status', 'active');
    }

    /**
     * Returns the identification value for the driver depending on type.
     */
    public function getIdentificationAttribute()
    {
        return $this->driver_type === 'Civil' ? $this->nic : $this->regiment_no;
    }

    public function driverAssignments()
    {
        return $this->hasMany(BusDriverAssignment::class, 'driver_id');
    }
}
