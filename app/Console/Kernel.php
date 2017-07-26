<?php

namespace App\Console;

use App\Console\Commands\AddAdmin;
use App\Console\Commands\AddMediaOffer;
use App\Console\Commands\AddOneTulipOffer;
use App\Console\Commands\OfferCron;
use App\Console\Commands\ProcessVirtualClicks;
use App\Console\Commands\RemoveInactiveLead;
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
        AddAdmin::class,
      //  AddMediaOffer::class,
       // AddOneTulipOffer::class,
        OfferCron::class,
        RemoveInactiveLead::class,
        ProcessVirtualClicks::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        /*$schedule->command('add:media')
            ->appendOutputTo(storage_path('logs/add_media.log'))
            ->withoutOverlapping()
            ->everyThirtyMinutes();

        $schedule->command('add:one')
            ->appendOutputTo(storage_path('logs/add_one_tulip.log'))
            ->withoutOverlapping()
            ->everyThirtyMinutes();*/

        $schedule->command('offer:cron')
            ->appendOutputTo(storage_path('logs/offer_cron.log'))
            ->withoutOverlapping()
            ->everyTenMinutes();


    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
