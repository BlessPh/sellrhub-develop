<?php

use App\Console\Commands\SendEmailCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Schedule::command(SendEmailCommand::class)->yearlyOn(12);
Schedule::command('app:update-best-seller-command')->hourly();
Schedule::command('app:update-new-arrivals-command')->daily();
Schedule::command('app:update-product-of-the-day-command')->everyFiveMinutes();
Schedule::command('app:update-featured-products-command')->daily();
