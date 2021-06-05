<?php

namespace App\Console;

use App\Console\Commands\BusinessFixHtmlEntities;
use App\Console\Commands\BusinessFixUtf8;
use App\Console\Commands\BusinessGenerateGeoCommand;
use App\Console\Commands\CalculateBusinessOpenStatusCommand;
use App\Console\Commands\PlaceShowWrongCoords;
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
        PlaceShowWrongCoords::class,
        BusinessFixUtf8::class,
        BusinessFixHtmlEntities::class,
        BusinessGenerateGeoCommand::class,
        CalculateBusinessOpenStatusCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         $schedule->command('business:hours:calculate')->everyMinute();
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
