<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DestinationLocation extends Model
{
    protected $table = 'destination_locations';

    protected $fillable = [
        'destination_location',
    ];
}