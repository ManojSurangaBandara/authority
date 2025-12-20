<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'e_no',
        'password',
        'establishment_id',
        'regiment_no',
        'rank',
        'contact_no',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Get the establishment (branch/directorate)
     */
    public function establishment(): BelongsTo
    {
        return $this->belongsTo(Establishment::class);
    }

    /**
     * Check if user is from branch/directorate
     */
    public function isBranchUser(): bool
    {
        return $this->hasAnyRole([
            'Bus Pass Subject Clerk (Branch)',
            'Staff Officer (Branch)',
            'Director (Branch)'
        ]);
    }

    /**
     * Check if user is from movement
     */
    public function isMovementUser(): bool
    {
        return $this->hasAnyRole([
            'Subject Clerk (DMOV)',
            'Staff Officer 2 (DMOV)',
            'Staff Officer 1 (DMOV)',
            'Col Mov (DMOV)',
            'Director (DMOV)',
            'Bus Escort (DMOV)'
        ]);
    }

    /**
     * Get user's hierarchy level for approval workflow
     */
    public function getHierarchyLevel(): int
    {
        if ($this->hasRole('Bus Pass Subject Clerk (Branch)')) return 1;
        if ($this->hasRole('Staff Officer (Branch)')) return 2;
        if ($this->hasRole('Director (Branch)')) return 3;
        if ($this->hasRole('Subject Clerk (DMOV)')) return 4;
        if ($this->hasRole('Staff Officer 2 (DMOV)')) return 5;
        if ($this->hasRole('Staff Officer 1 (DMOV)')) return 6;
        if ($this->hasRole('Col Mov (DMOV)')) return 7;
        if ($this->hasRole('Director (DMOV)')) return 7;
        if ($this->hasRole('Bus Escort (DMOV)')) return 9;

        return 0;
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Get the profile URL for AdminLTE
     */
    public function adminlte_profile_url(): string
    {
        return route('profile.show');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'e_no' => $this->e_no,
            'roles' => $this->roles->pluck('name')->toArray(),
            'establishment_id' => $this->establishment_id,
        ];
    }
}
