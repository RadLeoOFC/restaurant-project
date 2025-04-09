<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Desk;
use App\Models\Reservation;
use Carbon\Carbon;

class UpdateDeskStatuses extends Command
{
    protected $signature = 'desks:update-statuses';
    protected $description = 'Update desk statuses based on current reservations';

    public function handle()
    {
        $now = now();
        $dateToday = $now->toDateString();

        $this->info("Updating desk statuses for {$dateToday} at {$now->format('H:i:s')}...");

        $desks = Desk::all();

        foreach ($desks as $desk) {
            $reservations = Reservation::where('desk_id', $desk->id)
                ->where('reservation_date', $dateToday)
                ->get();

            $isOccupied = $reservations->some(function ($res) use ($now) {
                $start = Carbon::parse($res->reservation_time);
                $end = $start->copy()->addHours((int)($res->duration_hours ?? 2));
                return $now->between($start, $end);
            });

            if ($isOccupied) {
                if ($desk->status !== 'occupied') {
                    $desk->status = 'occupied';
                    $desk->save();
                    $this->info("Desk {$desk->name} set to OCCUPIED");
                }
            } else {
                if ($desk->status === 'occupied') {
                    $desk->status = 'available';
                    $desk->save();
                    $this->info("Desk {$desk->name} set to AVAILABLE");
                }
            }
        }

        $this->info('Desk status update completed.');
    }
}
