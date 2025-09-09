<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusRoute extends Model
{
    protected $table = 'bus_routes';

    protected $fillable = [
        'name',
        'bus_id',
    ];

    public function bus()
    {
        return $this->belongsTo(Bus::class, 'bus_id');
    }
}
