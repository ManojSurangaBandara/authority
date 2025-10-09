<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GsDivision extends Model
{
    protected $table = 'gs_divisions';
    
    protected $fillable = [
        'name',
    ];
}
