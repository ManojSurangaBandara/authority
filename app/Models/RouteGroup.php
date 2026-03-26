<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function members()
    {
        return $this->hasMany(RouteGroupMember::class);
    }

    public function livingOutRoutes()
    {
        return $this->members()->where('route_type', 'living_out');
    }

    public function livingInRoutes()
    {
        return $this->members()->where('route_type', 'living_in');
    }
}
