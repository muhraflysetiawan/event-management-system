<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'student_id',
        'phone',
        'avatar',
        'department',
        'organization',
        'is_active',
        'signature',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function createdEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    // Role helpers
    public function isAdmin(): bool
    {
        return $this->role?->slug === 'admin';
    }

    public function isCommittee(): bool
    {
        return $this->role?->slug === 'committee';
    }

    public function isStudent(): bool
    {
        return $this->role?->slug === 'student';
    }

    public function isLecturer(): bool
    {
        return $this->role?->slug === 'lecturer';
    }

    public function isStaff(): bool
    {
        return $this->role?->slug === 'staff';
    }

    public function isExternal(): bool
    {
        return $this->role?->slug === 'external';
    }

    public function isHeadDepartment(): bool
    {
        return in_array($this->role?->slug, [
            'head_csdl', 'head_baak', 'head_finance', 
            'head_gsd', 'head_sis', 'head_learning'
        ]);
    }

    public function isACOO(): bool
    {
        return $this->role?->slug === 'acoo';
    }

    public function hasRole(string $role): bool
    {
        return $this->role?->slug === $role;
    }
}
