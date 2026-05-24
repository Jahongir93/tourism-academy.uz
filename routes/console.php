<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send exam deadline reminders every 6 hours
Schedule::command('notifications:exam-reminders --hours=24')
    ->name('exam-reminders')
    ->everySixHours()
    ->withoutOverlapping()
    ->runInBackground();
