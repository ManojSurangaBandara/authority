<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlcmpIncharge extends Model
{
    protected $fillable = [
        'regiment_no',
        'rank',
        'name',
        'contact_no'
    ];

    public function slcmpInchargeAssignment()
    {
        return $this->hasOne(SlcmpInchargeAssignment::class, 'slcmp_incharge_id')
            ->where('status', 'active');
    }

    public function slcmpInchargeAssignments()
    {
        return $this->hasMany(SlcmpInchargeAssignment::class, 'slcmp_incharge_id');
    }
}
