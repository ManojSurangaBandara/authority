<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaritalStatus extends Model
{
    protected $fillable = [
        'status',
    ];

    // Since this is view-only, we can add a method to prevent creation/updates
    public static function boot()
    {
        parent::boot();

        // Prevent creating new records
        static::creating(function () {
            return false;
        });

        // Prevent updating records
        static::updating(function () {
            return false;
        });

        // Prevent deleting records
        static::deleting(function () {
            return false;
        });
    }
}
