<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function generate($type)
    {
        $startDate = now();

        switch ($type) {
            case 'daily':
                $endDate = $startDate->copy()->endOfDay();
                break;
            case 'weekly':
                $endDate = $startDate->copy()->endOfWeek();
                break;
            case 'monthly':
                $endDate = $startDate->copy()->endOfMonth();
                break;
            default:
                abort(404);
        }

        $reservations = Reservation::with('desk', 'customer')
            ->whereBetween('reservation_date', [$startDate, $endDate])
            ->get();

        return view('reports.show', compact('reservations', 'type'));
    }


    public function chartData(Request $request)
    {
        $type = $request->get('type', 'daily');
        $labels = [];
        $data = [];

        $now = Carbon::now();

        for ($i = 6; $i >= 0; $i--) {
            $date = match ($type) {
                'weekly' => $now->copy()->subWeeks($i)->startOfWeek(),
                'monthly' => $now->copy()->subMonths($i)->startOfMonth(),
                'yearly' => $now->copy()->subYears($i)->startOfYear(),
                default => $now->copy()->subDays($i),
            };

            $label = match ($type) {
                'weekly' => $date->format('W-Y'),
                'monthly' => $date->format('F Y'),
                'yearly' => $date->format('Y'),
                default => $date->format('d.m.Y'),
            };

            $start = $date;
            $end = match ($type) {
                'weekly' => $date->copy()->endOfWeek(),
                'monthly' => $date->copy()->endOfMonth(),
                'yearly' => $date->copy()->endOfYear(),
                default => $date->copy()->endOfDay(),
            };

            $count = Reservation::whereDate('reservation_date', '>=', $start->toDateString())
                   ->whereDate('reservation_date', '<=', $end->toDateString())
                   ->count();

            $labels[] = $label;
            $data[] = $count;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }
}
