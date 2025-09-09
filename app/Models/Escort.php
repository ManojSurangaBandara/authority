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
}
