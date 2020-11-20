<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('read:hotspot --all')
            ->everyThirtyMinutes()
            ->onOneServer()
            ->runInBackground();

        $schedule->command('horizon:snapshot')
            ->everyFiveMinutes()
            ->onOneServer()
            ->runInBackground();

        $schedule->command('passport:purge')
            ->hourly()
            ->onOneServer()
            ->runInBackground();

        $schedule->command('clear:logs --days=3')
            ->daily()
            ->onOneServer()
            ->runInBackground();

        $schedule->command('activitylog:clean')
            ->daily()
            ->onOneServer()
            ->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
