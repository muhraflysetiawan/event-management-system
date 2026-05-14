<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $fillable = [
        'event_id', 'created_by', 'title', 'content', 'type',
        'summary', 'total_participants', 'total_attended',
        'budget_allocated', 'total_expenses', 'financial_notes',
        'management_feedback', 'management_feedback_by', 'management_feedback_at',
    ];

    protected $casts = [
        'management_feedback_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function feedbackBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'management_feedback_by');
    }
}
