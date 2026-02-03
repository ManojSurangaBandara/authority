<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchCardSwitchHistory extends Model
{
    protected $fillable = [
        'bus_pass_application_id',
        'regiment_no',
        'old_branch_card_id',
        'old_temp_card_qr',
        'new_branch_card_id',
        'action',
        'remarks',
        'performed_by',
    ];

    // Relationship with BusPassApplication
    public function busPassApplication()
    {
        return $this->belongsTo(BusPassApplication::class);
    }

    // Relationship with User who performed the action
    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
