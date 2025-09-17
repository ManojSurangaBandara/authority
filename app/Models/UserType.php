<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserType extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category',
        'description',
        'permissions',
        'hierarchy_level',
        'is_active'
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Get users with this user type
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if this user type has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? []);
    }

    /**
     * Get branch/directorate user types
     */
    public static function branchTypes()
    {
        return static::where('category', 'branch')->where('is_active', true)->get();
    }

    /**
     * Get movement user types
     */
    public static function movementTypes()
    {
        return static::where('category', 'movement')->where('is_active', true)->get();
    }

    /**
     * Get user types ordered by hierarchy for approval workflow
     */
    public static function byHierarchy()
    {
        return static::where('is_active', true)
            ->where('hierarchy_level', '>', 0)
            ->orderBy('hierarchy_level')
            ->get();
    }
}
