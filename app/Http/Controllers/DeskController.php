<?php

namespace App\Http\Controllers;

use App\Models\Desk;
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

        return redirect()->route('desks.index')->with('success', 'Desk updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Desk $desk)
    {
        $desk->delete();
        return redirect()->route('desks.index')->with('success', 'Desk deleted successfully.');
    }
}
