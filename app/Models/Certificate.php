<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    protected $fillable = [
        'certificate_number', 'user_id', 'event_id', 'type', 'achievement_title', 'status', 'file_path',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public static function generateCertificateNumber($eventId): string
    {
        $event = Event::find($eventId);
        $count = self::where('event_id', $eventId)->count();
        $seq = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        
        $eventCode = self::generateEventCode($event->title);
        $monthRoman = self::toRoman(now()->format('n'));
        $day = now()->format('d');
        $year = now()->format('Y');

        return "{$seq}/{$eventCode}/{$monthRoman}/{$day}/{$year}";
    }

    public static function generateEventCode($title): string
    {
        // Remove numbers and clean string
        $cleanTitle = preg_replace('/[0-9]+/', '', $title);
        $words = array_values(array_filter(explode(' ', trim($cleanTitle))));
        $wordCount = count($words);

        if ($wordCount === 1) {
            $w = $words[0];
            $len = strlen($w);
            if ($len < 3) return strtoupper(str_pad($w, 3, 'X'));
            $first = $w[0];
            $middle = $w[(int)floor($len / 2)];
            $last = $w[$len - 1];
            return strtoupper($first . $middle . $last);
        } elseif ($wordCount === 2) {
            $w1 = $words[0];
            $w2 = $words[1];
            $first = $w1[0];
            $second = $w2[0];
            $third = $w2[strlen($w2) - 1];
            return strtoupper($first . $second . $third);
        } else { // 3 or more words
            $first = $words[0][0] ?? 'X';
            $second = $words[1][0] ?? 'X';
            $third = $words[2][0] ?? 'X';
            return strtoupper($first . $second . $third);
        }
    }

    private static function toRoman($number): string
    {
        $map = [
            'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'
        ];
        return $map[$number - 1] ?? 'I';
    }
    
    public static function createPendingForEvent(Event $event)
    {
        $isDesignReady = $event->certificate_template && $event->lecturer_id && $event->organizer_signature;
        $newStatus = $isDesignReady ? 'available' : 'pending';
        
        $participants = $event->participants()->where('status', 'accepted')->get();
        foreach ($participants as $participant) {
            $cert = self::where('user_id', $participant->user_id)
                        ->where('event_id', $event->id)
                        ->where('type', 'participation')
                        ->first();
            
            if ($cert) {
                if ($cert->status === 'pending' && $isDesignReady) {
                    $cert->update(['status' => 'available']);
                    \App\Services\NotificationService::notifyCertificateAvailable($participant->user, $event);
                }
            } else {
                $cert = self::create([
                    'user_id' => $participant->user_id,
                    'event_id' => $event->id,
                    'type' => 'participation',
                    'certificate_number' => self::generateCertificateNumber($event->id),
                    'status' => $newStatus
                ]);
                
                if ($newStatus === 'available') {
                    \App\Services\NotificationService::notifyCertificateAvailable($participant->user, $event);
                }
            }
        }
    }
}
