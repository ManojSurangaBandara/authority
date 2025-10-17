<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $table = 'persons';

    protected $fillable = [
        'regiment_no',
        'rank',
        'name',
        'unit',
        'nic',
        'army_id',
        'permanent_address',
        'telephone_no',
        'province_id',
        'district_id',
        'gs_division_id',
        'police_station_id',
    ];

    // Relationship with Province
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    // Relationship with District
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    // Relationship with GsDivision
    public function gsDivision()
    {
        return $this->belongsTo(GsDivision::class);
    }

    // Relationship with PoliceStation
    public function policeStation()
    {
        return $this->belongsTo(PoliceStation::class);
    }

    // Relationship with BusPassApplication
    public function busPassApplications()
    {
        return $this->hasMany(BusPassApplication::class);
    }
}
