<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = [
        'regiment_no',
        'rank',
        'name',
        'contact_no'
    ];
}
