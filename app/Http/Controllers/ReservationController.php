<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Desk;
use App\Models\Customer;
use App\Models\NotificationTemplate;
use Illuminate\Http\Request;
use App\Notifications\ReservationNotification;
use App\Models\User;

class ReservationController extends Controller
{
    /**
     * Display a listing of reservations.
     */
    public function index()
    {
        $reservations = Reservation::all();
        return view('reservations.index', compact('reservations'));
    }

    /**
     * Show the form for creating a new reservation.
     */
    public function create()
    {
        $desks = Desk::all();
        $customers = Customer::all();
        return view('reservations.create', compact('desks', 'customers'));
    }

    /**
     * Store a newly created reservation in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'desk_id' => 'required|exists:desks,id',
            'customer_id' => 'required|exists:customers,id',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required',
            'status' => 'required|in:new,confirmed,cancelled',
        ]);

        // Check if desk is already reserved
        $exists = Reservation::where('desk_id', $request->desk_id)
            ->where('reservation_date', $request->reservation_date)
            ->where('reservation_time', $request->reservation_time)
            ->exists();

        if ($exists) {
            return back()->withErrors(['desk_id' => 'This desk is already reserved at the selected time.']);
        }

        // Create reservation
        $reservation = Reservation::create([
            'desk_id' => $request->desk_id,
            'customer_id' => $request->customer_id,
            'reservation_date' => $request->reservation_date,
            'reservation_time' => $request->reservation_time,
            'status' => $request->status,
        ]);

        // Get customer's preferred language
        $customer = Customer::find($request->customer_id);
        $language = $customer->preferred_language ?? 'en';

        // Get notification template
        $template = NotificationTemplate::where('key', 'reservation_created')
            ->where('language_code', $language)
            ->first();

        $message = $template->body ?? 'Reservation created successfully.';

        // Send notification to current user (or change to customer if needed)
        auth()->user()->notify(new ReservationNotification($message));

        return redirect()->route('reservations.index')->with('success', 'Reservation created.');
    }

    /**
     * Show the form for editing the specified reservation.
     */
    public function edit(Reservation $reservation)
    {
        $desks = Desk::all();
        $customers = Customer::all();
        return view('reservations.edit', compact('reservation', 'desks', 'customers'));
    }

    /**
     * Update the specified reservation in storage.
     */
    public function update(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'desk_id' => 'required|exists:desks,id',
            'customer_id' => 'required|exists:customers,id',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required',
            'status' => 'required|in:new,confirmed,cancelled',
        ]);

        // Update reservation
        $reservation->update($validated);

        // Get customer's preferred language
        $customer = Customer::find($request->customer_id);
        $language = $customer->preferred_language ?? 'en';

        // Get notification template
        $template = NotificationTemplate::where('key', 'reservation_updated')
            ->where('language_code', $language)
            ->first();

        $message = $template->body ?? 'Reservation updated successfully.';

        // Send notification to current user
        auth()->user()->notify(new ReservationNotification($message));

        return redirect()->route('reservations.index')->with('success', 'Reservation updated.');
    }

    /**
     * Remove the specified reservation from storage.
     */
    public function destroy(Reservation $reservation)
    {
        $customer = $reservation->customer;
        $language = $customer->preferred_language ?? 'en';

        $template = NotificationTemplate::where('key', 'reservation_cancelled')
            ->where('language_code', $language)
            ->first();

        $message = $template->body ?? 'Reservation cancelled.';

        // Delete reservation
        $reservation->delete();

        // Send notification to current user
        auth()->user()->notify(new ReservationNotification($message));

        return redirect()->route('reservations.index')->with('success', 'Reservation deleted.');
    }
}
