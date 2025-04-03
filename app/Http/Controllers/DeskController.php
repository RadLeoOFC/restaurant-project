<?php

namespace App\Http\Controllers;

use App\Models\Desk;
use App\Models\ExternalDesk;
use App\Models\DeskSnapshot;
use Illuminate\Http\Request;

class DeskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $desks = Desk::all();
        return view('desks.index', compact('desks'));
    }

    public function map()
    {
        $desks = Desk::all();
        $externalDesks = ExternalDesk::all();
    
        return view('desks.map', compact('desks', 'externalDesks'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('desks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,selected',
            'coordinates_x' => 'required|integer',
            'coordinates_y' => 'required|integer',
        ]);

        Desk::create($validated);

        return redirect()->route('desks.index')->with('success', 'Desk added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Desk $desk)
    {
        return view('desks.edit', compact('desk'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Desk $desk)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'capacity' => 'sometimes|integer|min:1',
            'status' => 'sometimes|in:available,occupied,selected',
            'coordinates_x' => 'sometimes|integer',
            'coordinates_y' => 'sometimes|integer',
        ]);
    
        $desk->update($validated);
    
        // Явно проверяем: AJAX или обычный запрос
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
    
        return redirect()->route('desks.index')->with('success', 'Desk updated successfully.');
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Desk $desk)
    {
        $desk->delete();
    
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
    
        return redirect()->route('desks.index')->with('success', 'Desk deleted successfully.');
    }   
    
    public function saveSnapshot()
    {
        $date = now()->toDateString();
    
        // Delete existing snapshots for today (to avoid duplicates)
        DeskSnapshot::where('snapshot_date', $date)->delete();
    
        foreach (Desk::all() as $desk) {
            DeskSnapshot::create([
                'desk_id' => $desk->id,
                'coordinates_x' => $desk->coordinates_x,
                'coordinates_y' => $desk->coordinates_y,
                'snapshot_date' => $date,
            ]);
        }
    
        return redirect()->back()->with('success', 'A snapshot of the current layout has been saved.');
    }    
}
