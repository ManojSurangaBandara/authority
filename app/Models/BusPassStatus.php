<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusPassStatus extends Model
{
    protected $fillable = [
        'code',
        'label',
        'badge_color',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Scope for active statuses
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for ordered statuses
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Get badge HTML
    public function getBadgeHtmlAttribute()
    {
        return '<span class="badge badge-' . $this->badge_color . '">' . $this->label . '</span>';
    }

    // Find status by code
    public static function findByCode($code)
    {
        return static::where('code', $code)->first();
    }

    // Get all active statuses as array for select options
    public static function getSelectOptions()
    {
        return static::active()->ordered()->pluck('label', 'code')->toArray();
    }
}
