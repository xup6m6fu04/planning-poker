<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('rooms:broadcast-states')->everyTwoSeconds();
Schedule::command('users:cleanup-inactive')->everyTenSeconds();