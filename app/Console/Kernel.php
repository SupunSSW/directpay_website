<?php

namespace App\Console;

use App\Scheduler\CardExpiryReport;
use App\Scheduler\DailyDownloadReport;
use App\Scheduler\dailyPreDayTrnRepo;
use App\Scheduler\RecurringFailedReport;
use App\Scheduler\WalletBalanceNotification;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Class Kernel.
 */
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
        //Daily download report
        //$schedule->call(new DailyDownloadReport)->dailyAt('08:00');

        //Card Expiry list
        //$schedule->call(new CardExpiryReport)->monthlyOn(1, '08:00');

        //Recurring payment
        //$schedule->call(new RecurringFailedReport)->dailyAt('08:00');

        //ALL_TRANSACTIONS_MADE_THE_PREVIOUS_DAY_
        $schedule->call(new dailyPreDayTrnRepo())->dailyAt('10:00');

        //WalletBalanceNotification
        $schedule->call(new WalletBalanceNotification())->everyTenMinutes();
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
