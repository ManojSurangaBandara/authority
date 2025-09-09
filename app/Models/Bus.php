<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    protected $fillable = [
        'no',
        'name',
        'type_id',
        'no_of_seats',
    ];

    // public function type()
    // {
    //     return $this->belongsTo(BusType::class, 'type_id');
    // }
}
