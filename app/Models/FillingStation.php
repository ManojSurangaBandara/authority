<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FillingStation extends Model
{
    protected $fillable = [
        'name',
    ];

    public function fillingStationAssignment()
    {
        return $this->hasOne(BusFillingStationAssignment::class, 'filling_station_id')
            ->where('status', 'active');
    }

    public function fillingStationAssignments()
    {
        return $this->hasMany(BusFillingStationAssignment::class, 'filling_station_id');
    }
}
