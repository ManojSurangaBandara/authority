<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Escort extends Model implements JWTSubject
{
    protected $fillable = [
        'regiment_no',
        'eno',
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

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [
            'escort_id' => $this->id,
            'regiment_no' => $this->regiment_no,
            'eno' => $this->eno,
            'name' => $this->name,
            'rank' => $this->rank,
            'type' => 'escort',
        ];
    }
}
