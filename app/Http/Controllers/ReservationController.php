<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Desk;
use App\Models\Customer;
use App\Models\NotificationTemplate;
use Illuminate\Http\Request;
use App\Notifications\ReservationNotification;

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
        // Validate request input
        $validated = $request->validate([
            'desk_id' => 'required|exists:desks,id',
            'customer_id' => 'required|exists:customers,id',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required',
            'status' => 'required|in:new,confirmed,cancelled',
        ]);

        // Check if desk is already reserved at given date and time
        $exists = Reservation::where('desk_id', $request->desk_id)
            ->where('reservation_date', $request->reservation_date)
            ->where('reservation_time', $request->reservation_time)
            ->exists();

        if ($exists) {
            return back()->withErrors(['desk_id' => 'This desk is already reserved at the selected time.']);
        }

        // Create reservation
        $reservation = Reservation::create($validated);

        // Send notification to customer
        $this->notifyCustomer($reservation->customer, 'reservation_created');

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
        // Validate input
        $validated = $request->validate([
            'desk_id' => 'required|exists:desks,id',
            'customer_id' => 'required|exists:customers,id',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required',
            'status' => 'required|in:new,confirmed,cancelled',
        ]);

        // Update reservation
        $reservation->update($validated);

        // Send update notification
        $this->notifyCustomer($reservation->customer, 'reservation_updated');

        return redirect()->route('reservations.index')->with('success', 'Reservation updated.');
    }

    /**
     * Remove the specified reservation from storage.
     */
    public function destroy(Reservation $reservation)
    {
        $customer = $reservation->customer;

        // Delete reservation
        $reservation->delete();

        // Send cancellation notification
        $this->notifyCustomer($customer, 'reservation_cancelled');

        return redirect()->route('reservations.index')->with('success', 'Reservation deleted.');
    }

    /**
     * Notify customer using language-specific template.
     */
    protected function notifyCustomer(Customer $customer, string $templateKey)
    {
        $language = $customer->preferred_language ?? 'en';
        $notification = new ReservationNotification($templateKey, $language);
    
        // Уведомляем клиента
        $customer->notify($notification);
    
        // Также уведомляем администратора
        if (auth()->check()) {
            auth()->user()->notify($notification);
        }
    }    
}
