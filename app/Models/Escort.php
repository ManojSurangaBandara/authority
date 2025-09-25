<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Escort extends Model
{
    protected $fillable = [
        'regiment_no',
        'rank',
        'name',
        'contact_no'
    ];

    public function escortAssignment()
    {
        return $this->hasOne(BusEscortAssignment::class, 'escort_id')
            ->where('status', 'active');
    }

    public function escortAssignments()
    {
        return $this->hasMany(BusEscortAssignment::class, 'escort_id');
    }
}
