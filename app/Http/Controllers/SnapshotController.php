<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SnapshotController extends Controller
{
    // Return a list of unique snapshot dates
    public function list()
    {
        try {
            $dates = DB::table('desk_snapshots')
                ->select('snapshot_date')
                ->distinct()
                ->orderByDesc('snapshot_date')
                ->get();
    
            return response()->json($dates);
        } catch (\Throwable $e) {
            \Log::error('Ошибка в SnapshotController@list: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }
    

    // Load a specific snapshot based on the selected date
    public function load(Request $request)
    {
        $date = $request->input('snapshot_date');

        $snapshotData = DB::table('desk_snapshots')
            ->where('snapshot_date', $date)
            ->get();

        return response()->json($snapshotData);
    }

    // Reset the layout to today's snapshot if available
    public function reset()
    {
        $today = now()->toDateString();
    
        // Получаем снапшоты за сегодня
        $snapshots = DB::table('desk_snapshots')
            ->whereDate('snapshot_date', $today)
            ->get();
    
        // Применяем их к таблице desks
        foreach ($snapshots as $snapshot) {
            DB::table('desks')
                ->where('id', $snapshot->desk_id)
                ->update([
                    'coordinates_x' => $snapshot->coordinates_x,
                    'coordinates_y' => $snapshot->coordinates_y,
                    'updated_at' => now()
                ]);
        }
    
        // Получаем свежие данные для отрисовки UI
        $updatedDesks = DB::table('desks')->get();
    
        return response()->json([
            'success' => true,
            'desks' => $updatedDesks->map(function ($desk) {
                return [
                    'desk_id' => $desk->id,
                    'name' => $desk->name,
                    'capacity' => $desk->capacity,
                    'status' => $desk->status,
                    'coordinates_x' => $desk->coordinates_x,
                    'coordinates_y' => $desk->coordinates_y,
                ];
            }),
        ]);
        
    }        
}
