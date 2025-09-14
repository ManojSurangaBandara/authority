<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusFillingStationAssignment extends Model
{
    use HasFactory;

    protected $table = 'bus_filling_station_assignments';

    protected $fillable = [
        'bus_id',
        'filling_station_id',
        'assigned_date',
        'end_date',
        'status',
        'created_by',
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the bus that this filling station is assigned to.
     */
    public function bus()
    {
        return $this->belongsTo(Bus::class, 'bus_id');
    }

    /**
     * Get the filling station assigned to the bus.
     */
    public function fillingStation()
    {
        return $this->belongsTo(FillingStation::class, 'filling_station_id');
    }

    /**
     * Get the status badge HTML.
     */
    public function getStatusBadgeAttribute()
    {
        switch ($this->status) {
            case 'active':
                return '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Active</span>';
            case 'inactive':
                return '<span class="badge badge-secondary"><i class="fas fa-pause-circle"></i> Inactive</span>';
            default:
                return '<span class="badge badge-warning"><i class="fas fa-question-circle"></i> Unknown</span>';
        }
    }

    /**
     * Scope to get active assignments.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get inactive assignments.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Check if assignment is currently active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Get formatted assigned date.
     */
    public function getFormattedAssignedDateAttribute()
    {
        return $this->assigned_date ? $this->assigned_date->format('d M Y') : 'N/A';
    }

    /**
     * Get formatted end date.
     */
    public function getFormattedEndDateAttribute()
    {
        return $this->end_date ? $this->end_date->format('d M Y') : 'Ongoing';
    }
}
