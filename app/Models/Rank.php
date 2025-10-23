<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
    protected $fillable = [
        'abb_name',
        'full_name',
    ];

    // Relationship with Person
    public function persons()
    {
        return $this->hasMany(Person::class);
    }
}
