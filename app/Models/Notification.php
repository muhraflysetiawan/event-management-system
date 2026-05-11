<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id', 'event_id', 'type', 'title', 'message', 'is_read',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function getUrlAttribute(): string
    {
        if ($this->type === 'certificate') {
            return route('certificates.index');
        }

        if ($this->event_id) {
            if ($this->type === 'event' && str_contains($this->title, 'Approval Request')) {
                return route('events.approvals');
            }
            if ($this->type === 'event' && str_contains($this->title, 'Fully Approved')) {
                return route('events.show', $this->event_id) . '#publish-section';
            }
            return route('events.show', $this->event_id);
        }

        return route('dashboard');
    }
}
