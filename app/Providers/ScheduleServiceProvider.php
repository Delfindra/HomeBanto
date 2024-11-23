<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class ScheduleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            // Jalankan command setiap hari jam 8 pagi
            $schedule->command('check:food-expiration')
                     ->dailyAt('00.00')
                     ->timezone('Asia/Jakarta');
        });
    }
}
