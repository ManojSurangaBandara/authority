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
        'grama_seva_division',
        'nearest_police_station',
    ];

    // Relationship with Province
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    // Relationship with BusPassApplication
    public function busPassApplications()
    {
        return $this->hasMany(BusPassApplication::class);
    }
}
