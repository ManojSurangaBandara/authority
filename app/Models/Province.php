<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $table = 'provinces';

    protected $fillable = [
        'name',
    ];

    // Relationship with Person
    public function persons()
    {
        return $this->hasMany(Person::class);
    }
}
