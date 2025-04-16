<?php

namespace App\Http\Controllers;

use App\Models\Desk;
use App\Models\ExternalDesk;
use App\Models\DeskSnapshot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
        if (!Auth::user()->hasRole('Admin')) {
            abort(403);
        }
        return view('desks.create');
    }
    
    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('Admin')) {
            abort(403);
        }
    
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,selected',
            'coordinates_x' => 'required|integer',
            'coordinates_y' => 'required|integer',
        ]);
    
        Desk::create($validated);
    
        return redirect()->route('desks.index')->with('success', __('messages.desk_added'));
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
        if (!Auth::user()->hasRole('Admin')) {
            abort(403);
        }

        return view('desks.edit', compact('desk'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Desk $desk)
    {
        if (!Auth::user()->hasRole('Admin')) {
            abort(403);
        }

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
    
        return redirect()->route('desks.index')->with('success', __('messages.desk_updated'));
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Desk $desk)
    {
        if (!Auth::user()->hasRole('Admin')) {
            abort(403);
        }

        $desk->delete();
    
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
    
        return redirect()->route('desks.index')->with('success', __('messages.desk_deleted'));
    }   
    
    public function saveSnapshot()
    {
        if (!Auth::user()->hasRole('Admin')) {
            abort(403);
        }
        
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
    
        return redirect()->back()->with('success', __('messages.layout_snapshot_saved'));
    }
    
    public function selectTemporary(Request $request)
    {
        $desk = Desk::find($request->desk_id);
        if (!$desk || $desk->status !== 'available') {
            return response()->json(['success' => false], 404);
        }

        $desk->status = 'selected';
        $desk->selected_until = Carbon::now()->addMinutes(15);
        $desk->save();

        return response()->json(['success' => true]);
    }
}
