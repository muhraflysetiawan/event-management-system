<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:remind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders for events starting in 2 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetDate = Carbon::now()->addDays(2)->toDateString();
        
        $events = Event::whereDate('start_date', $targetDate)
            ->where('status', 'published')
            ->with('participants.user')
            ->get();

        $count = 0;
        foreach ($events as $event) {
            foreach ($event->participants as $participant) {
                if ($participant->status === 'accepted') {
                    NotificationService::notifyEventReminder($participant->user, $event);
                    $count++;
                }
            }
        }

        $this->info("Sent {$count} reminders for " . $events->count() . " events.");
    }
}
