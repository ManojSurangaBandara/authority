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
        'grama_seva_division',
        'nearest_police_station',
    ];

    // Relationship with BusPassApplication
    public function busPassApplications()
    {
        return $this->hasMany(BusPassApplication::class);
    }
}
