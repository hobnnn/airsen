<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

//Schedule::command('evacuation:assign_center')->hourly();
//Schedule::command('demo:hello')->everyMinute();
Schedule::command('app:scan-high-alerts')->everyThirtyMinutes();