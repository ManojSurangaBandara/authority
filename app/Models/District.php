<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $table = 'districts';

    protected $fillable = [
        'name',
    ];

    // Relationship with Person
    public function persons()
    {
        return $this->hasMany(Person::class);
    }
}
