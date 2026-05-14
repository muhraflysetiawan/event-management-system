<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Event extends Model
{
    protected $fillable = [
        'title', 'description', 'project_brief_type', 'project_brief', 'project_brief_pdf', 'start_date', 'end_date',
        'location', 'quota', 'status', 'is_attendance_open', 'created_by',
        'qr_code', 'qr_token', 'qr_expires_at',
        'certificate_template', 'lecturer_id', 'organizer_signature',
        'head_id', 'head_signature', 'acoo_id', 'acoo_signature',
        'event_logo', 'target_audience', 'required_approval_roles',
        'approved_by_roles',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'qr_expires_at' => 'datetime',
            'target_audience' => 'array',
            'required_approval_roles' => 'array',
            'approved_by_roles' => 'array',
        ];
    }

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_id');
    }

    public function acoo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acoo_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class);
    }

    public function approvalLogs(): HasMany
    {
        return $this->hasMany(ApprovalLog::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(Revision::class);
    }

    public function survey(): HasOne
    {
        return $this->hasOne(Survey::class);
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(EventRequirement::class);
    }

    public function acceptedParticipants(): HasMany
    {
        return $this->hasMany(Participant::class)->where('status', 'accepted');
    }

    public function availableSlots(): int
    {
        return max(0, $this->quota - $this->acceptedParticipants()->count());
    }

    public function isFull(): bool
    {
        return $this->availableSlots() <= 0;
    }

    public function isQrValid(): bool
    {
        return $this->qr_token && $this->qr_expires_at && $this->qr_expires_at->isFuture();
    }
    public function isFullyApproved(): bool
    {
        $required = $this->required_approval_roles ?? [];
        $approved = $this->approved_by_roles ?? [];
        
        if (empty($required)) return true;
        
        foreach ($required as $role) {
            if (!in_array($role, $approved)) {
                return false;
            }
        }
        
        return true;
    }

    public function isApprovedByRole($roleSlug): bool
    {
        return in_array($roleSlug, $this->approved_by_roles ?? []);
    }

    public function getGeneratedIdAttribute(): string
    {
        $eventCode = Certificate::generateEventCode($this->title);
        $monthRoman = $this->toRoman($this->start_date->format('n'));
        $day = $this->start_date->format('d');
        $year = $this->start_date->format('Y');

        return "{$eventCode}/{$monthRoman}/{$day}/{$year}";
    }

    private function toRoman($number): string
    {
        $map = [
            'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'
        ];
        return $map[$number - 1] ?? 'I';
    }
}
