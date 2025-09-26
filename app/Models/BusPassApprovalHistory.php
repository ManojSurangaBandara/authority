<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusPassApprovalHistory extends Model
{
    protected $fillable = [
        'bus_pass_application_id',
        'user_id',
        'action',
        'previous_status',
        'new_status',
        'remarks',
        'action_date'
    ];

    protected $casts = [
        'action_date' => 'datetime'
    ];

    /**
     * Get the bus pass application
     */
    public function busPassApplication(): BelongsTo
    {
        return $this->belongsTo(BusPassApplication::class);
    }

    /**
     * Get the user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for ordered by action date
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('action_date', 'desc');
    }

    /**
     * Get action badge color
     */
    public function getActionBadgeAttribute()
    {
        $colors = [
            'approved' => 'success',
            'rejected' => 'danger',
            'forwarded' => 'info',
            'recommended' => 'success',
            'not_recommended' => 'warning'
        ];

        $labels = [
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'forwarded' => 'Forwarded',
            'recommended' => 'Recommended',
            'not_recommended' => 'Not Recommended'
        ];

        $color = $colors[$this->action] ?? 'secondary';
        $label = $labels[$this->action] ?? ucfirst($this->action);
        return '<span class="badge badge-' . $color . '">' . $label . '</span>';
    }
}
