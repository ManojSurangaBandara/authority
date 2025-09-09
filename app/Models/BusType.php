<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusType extends Model
{
    protected $table = 'bus_types';
    protected $fillable = ['name'];
}
