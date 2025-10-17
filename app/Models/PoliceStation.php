<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoliceStation extends Model
{
    protected $table = 'police_stations';

    protected $fillable = [
        'name',
    ];

    // Relationship with Person
    public function persons()
    {
        return $this->hasMany(Person::class);
    }
}
