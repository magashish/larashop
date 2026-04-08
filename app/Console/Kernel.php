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

      Commands\RefreshInstagramToken::class,
      Commands\Test_ProcessUnregisteredUserFollowUps::class,
      Commands\Test_WeeklyDrawReminder::class,

      Commands\GeneratePrizeDrawEntries::class,
      Commands\CheckSubscriptionPackageStatus::class,
      Commands\WeeklyDrawReminder::class,
      Commands\PackageExpiryReminder::class,
      Commands\RetargetPastCustomers::class,
      Commands\PackageEntryResetLogic::class,
      Commands\ProcessUnregisteredUserFollowUps::class,
      Commands\SyncStripeTransactions::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        // $schedule->command('prize:generate-entries')->everyMinute(); // Reminder: This schedule is currently removed because the functionality now runs on payment successful event.


          // $schedule->command('instagram:refresh-token')->monthly();

        // $schedule->command('app:test_process-unregistered-user-follow-ups')
        // ->everyMinute()
        // ->withoutOverlapping()
        // ->onOneServer();

        // $schedule->command('app:test-weekly-draw-reminder')
        // ->everyFiveMinutes()
        // ->withoutOverlapping()
        // ->onOneServer();


        //$schedule->command('app:weekly-draw-reminder')->dailyAt('21:01')->withoutOverlapping()->onOneServer();

           
          // $schedule->command('app:check-subscription-package-status')->everyMinute();

           
          // $schedule->command('app:weekly-draw-reminder')->everyFiveMinutes();
          // $schedule->command('app:retarget-past-customers')->everyFiveMinutes();
          // $schedule->command('app:package-expiry-reminder')->everyFiveMinutes();
           //$schedule->command('followups:process-unregistered')->everyMinute();
          // $schedule->command('app:package-entry-reset-logic')->everyMinute();
          


    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
