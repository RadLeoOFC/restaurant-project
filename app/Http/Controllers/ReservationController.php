<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Desk;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Display a listing of reservations.
     * This method shows all reservations in a table.
     */
    public function index()
    {
        $reservations = Reservation::all();
        return view('reservations.index', compact('reservations'));
    }

    /**
     * Show the form for creating a new reservation.
     * This method renders the reservation creation form.
     */
    public function create()
    {
        // Get all desks from database
        $desks = Desk::all();

        // Pass desks to the view
        return view('reservations.create', compact('desks'));
    }

    /**
     * Store a newly created reservation in storage.
     * This method validates input and checks desk availability before saving.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'desk_id' => 'required|exists:desks,id',
            'customer_name' => 'required|string',
            'contact' => 'required|string',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required',
            'status' => 'required|in:new,confirmed,cancelled',
        ]);

        // Check if desk is already reserved at the given date and time
        $exists = Reservation::where('desk_id', $request->desk_id)
            ->where('reservation_date', $request->reservation_date)
            ->where('reservation_time', $request->reservation_time)
            ->exists();

        if ($exists) {
            return back()->withErrors(['desk_id' => 'This desk is already reserved at the selected time.']);
        }

        // Create new reservation
        Reservation::create($validated);

        return redirect()->route('reservations.index')->with('success', 'Reservation created.');
    }

    /**
     * Display the specified reservation.
     * This method shows details of one reservation.
     */
    public function show(Reservation $reservation)
    {
        return view('reservations.show', compact('reservation'));
    }

    /**
     * Show the form for editing the specified reservation.
     * This method renders the edit form for a reservation.
     */
    public function edit(Reservation $reservation)
    {
        // Get all desks from database
        $desks = Desk::all();
        return view('reservations.edit', compact('reservation', 'desks'));
    }

    /**
     * Update the specified reservation in storage.
     * This method validates input and updates reservation info.
     */
    public function update(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'desk_id' => 'required|exists:desks,id',
            'customer_name' => 'required|string',
            'contact' => 'required|string',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required',
            'status' => 'required|in:new,confirmed,cancelled',
        ]);

        // Update reservation
        $reservation->update($validated);

        return redirect()->route('reservations.index')->with('success', 'Reservation updated.');
    }

    /**
     * Remove the specified reservation from storage.
     * This method deletes a reservation from the database.
     */
    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return redirect()->route('reservations.index')->with('success', 'Reservation deleted.');
    }
}
