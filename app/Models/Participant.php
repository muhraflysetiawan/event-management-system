<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Participant extends Model
{
    protected $fillable = [
        'registration_number', 'user_id', 'event_id', 'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public static function generateRegistrationNumber(Event $event, User $user): string
    {
        $date = now()->format('dmY');
        
        // Get initials from event title (e.g. Web Development Workshop -> WDW)
        $words = explode(' ', $event->title);
        $initials = '';
        foreach ($words as $w) {
            if (ctype_alpha(substr($w, 0, 1))) {
                $initials .= strtoupper(substr($w, 0, 1));
            }
        }
        if (empty($initials)) $initials = 'EVT';

        // Get last 3 digits of student_id
        $nim = '000';
        if ($user->student_id) {
            $nim = substr($user->student_id, -3);
            if (strlen($nim) < 3) {
                $nim = str_pad($nim, 3, '0', STR_PAD_LEFT);
            }
        }

        $baseNumber = "HS{$initials}{$date}{$nim}";
        $registrationNumber = $baseNumber;
        $counter = 1;
        
        while (self::where('registration_number', $registrationNumber)->exists()) {
            $registrationNumber = $baseNumber . str_pad($counter, 2, '0', STR_PAD_LEFT);
            $counter++;
        }

        return $registrationNumber;
    }
}
