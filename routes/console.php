<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('events:remind', function () {
    $this->call(\App\Console\Commands\SendEventReminders::class);
})->purpose('Send reminders for events starting in 2 days');

// Schedule it
\Illuminate\Support\Facades\Schedule::command('events:remind')->dailyAt('08:00');
