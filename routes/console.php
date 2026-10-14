<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes (Laravel 11+)
|--------------------------------------------------------------------------
| In Laravel 11, scheduled tasks are defined here instead of
| app/Console/Kernel.php (which no longer exists by default).
|
| The scheduler runs via: php artisan schedule:run
| For production, add a cron entry:  * * * * * php artisan schedule:run
|--------------------------------------------------------------------------
*/

// Schedule the seat hold release command to run every minute.
// This ensures expired holds are cleaned up automatically in production.
// For the demo, the SeatController also runs this logic on every page load.
Schedule::command('seats:release-expired')->everyMinute();

// Built-in example command
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
