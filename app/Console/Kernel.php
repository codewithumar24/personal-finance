<?php
// app/Console/Kernel.php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Process recurring transactions daily
        $schedule->command('finance:process-recurring-transactions')
                 ->dailyAt('06:00')
                 ->withoutOverlapping();

        // Check and send budget alerts daily
        $schedule->command('finance:check-budget-alerts')
                 ->dailyAt('08:00')
                 ->withoutOverlapping();

        // Check and send goal alerts daily
        $schedule->command('finance:check-goal-alerts')
                 ->dailyAt('09:00')
                 ->withoutOverlapping();

        // Send weekly summaries on Monday morning
        $schedule->command('finance:send-weekly-summaries')
                 ->weeklyOn(1, '09:00')
                 ->withoutOverlapping();

        // Send monthly reports on 1st of each month
        $schedule->command('finance:send-monthly-reports')
                 ->monthlyOn(1, '10:00')
                 ->withoutOverlapping();

        // Clean up old notifications monthly
        $schedule->command('finance:cleanup-notifications')
                 ->monthlyOn(1, '05:00')
                 ->withoutOverlapping();

        // Backup database daily
        $schedule->command('backup:clean')->daily()->at('01:00');
        $schedule->command('backup:run')->daily()->at('02:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        $this->loadModulesCommands();

        require base_path('routes/console.php');
    }

    protected function loadModulesCommands(): void
    {
        $modulesPath = base_path('modules');
        $modules = glob($modulesPath . '/*', GLOB_ONLYDIR);

        foreach ($modules as $module) {
            $commandPath = $module . '/Console/Commands';
            if (is_dir($commandPath)) {
                $this->load($commandPath);
            }
        }
    }
}