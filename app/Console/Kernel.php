<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('coins:reset-daily')
            ->dailyAt('00:00')
            ->timezone('Asia/Amman');

        $schedule->command('conversations:expire')
            ->dailyAt('00:01')
            ->timezone('Asia/Amman');

        $schedule->command('subscriptions:renew-check')
            ->dailyAt('00:02')
            ->timezone('Asia/Amman');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
