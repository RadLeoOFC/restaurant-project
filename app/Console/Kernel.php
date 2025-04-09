<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\UpdateDeskStatuses;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        UpdateDeskStatuses::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('desks:update-statuses')->everyMinute();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}