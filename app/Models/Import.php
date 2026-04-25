<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Import extends Model
{
    protected $fillable = [
        'user_id', 'type', 'file_path', 'status', 'processed_rows', 'total_rows', 'error_log',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
