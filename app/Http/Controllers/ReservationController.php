<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Desk;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Notifications\ReservationNotification;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $reservations = $user->hasRole('Admin')
            ? Reservation::all()
            : Reservation::whereHas('customer', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();

        return view('reservations.index', compact('reservations'));
    }

    public function create(Request $request)
    {
        $reservationDate = $request->input('reservation_date');
        $reservationTime = $request->input('reservation_time');
        $duration = (int) $request->input('duration_hours', 2);

        if (!$reservationDate || !$reservationTime) {
            $desks = Desk::all();
        } else {
            $startTime = Carbon::parse($reservationTime);
            $endTime = $startTime->copy()->addHours($duration);

            $busyDeskIds = Reservation::where('reservation_date', $reservationDate)
                ->get()
                ->filter(function ($res) use ($startTime, $endTime) {
                    $existingStart = Carbon::parse($res->reservation_time);
                    $existingEnd = $existingStart->copy()->addHours((int) ($res->duration_hours ?? 2));
                    return $startTime->lt($existingEnd) && $endTime->gt($existingStart);
                })
                ->pluck('desk_id');

            $desks = Desk::whereNotIn('id', $busyDeskIds)->get();
        }

        $customers = Customer::all();
        return view('reservations.create', compact('desks', 'customers'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'desk_id' => 'required|exists:desks,id',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required',
            'status' => 'required|in:new,confirmed,cancelled',
            'duration_hours' => 'required|integer|min:2|max:8',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        if (!$user->hasRole('Admin')) {
            $customer = $user->customer;
            if (!$customer) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('messages.customer_profile_missing'),
                    ], 422);
                }
        
                return redirect()->back()->withErrors(['customer_id' => __('messages.customer_profile_missing')]);
            }
        
            $validated['customer_id'] = $customer->id;
        }        

        $startTime = Carbon::parse($validated['reservation_time']);
        $endTime = $startTime->copy()->addHours((int) $validated['duration_hours']);

        $conflict = Reservation::where('desk_id', $validated['desk_id'])
            ->where('reservation_date', $validated['reservation_date'])
            ->get()
            ->some(function ($existingReservation) use ($startTime, $endTime) {
                $existingStart = Carbon::parse($existingReservation->reservation_time);
                $existingEnd = $existingStart->copy()->addHours((int) ($existingReservation->duration_hours ?? 2));
                return $startTime->lt($existingEnd) && $endTime->gt($existingStart);
            });

        if ($conflict) {
            return back()->withErrors(['desk_id' => __('messages.desk_already_reserved')]);
        }

        $reservation = Reservation::create($validated);
        $this->updateDeskStatus($reservation->desk_id, $reservation->reservation_date);
        $this->notifyCustomer($reservation->customer, 'reservation_created');

        return redirect()->route('reservations.index')->with('success', __('messages.reservation_created'));
    }

    public function edit(Reservation $reservation, Request $request)
    {
        $user = auth()->user();

        if (!$user->hasRole('Admin') && $reservation->customer->user_id !== $user->id) {
            abort(403);
        }

        $startTime = Carbon::parse($reservation->reservation_time);
        $duration = (int) ($reservation->duration_hours ?? 2);
        $endTime = $startTime->copy()->addHours($duration);

        $busyDeskIds = Reservation::where('reservation_date', $reservation->reservation_date)
            ->where('id', '!=', $reservation->id)
            ->get()
            ->filter(function ($res) use ($startTime, $endTime) {
                $existingStart = Carbon::parse($res->reservation_time);
                $existingEnd = $existingStart->copy()->addHours((int) ($res->duration_hours ?? 2));
                return $startTime->lt($existingEnd) && $endTime->gt($existingStart);
            })
            ->pluck('desk_id');

        $desks = Desk::whereNotIn('id', $busyDeskIds)->orWhere('id', $reservation->desk_id)->get();
        $customers = $user->hasRole('Admin') ? Customer::all() : collect();

        return view('reservations.edit', compact('reservation', 'desks', 'customers'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $user = auth()->user();

        if (!$user->hasRole('Admin') && $reservation->customer->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'desk_id' => 'required|exists:desks,id',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required',
            'status' => 'required|in:new,confirmed,cancelled',
            'duration_hours' => 'required|integer|min:2|max:8',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        if (!$user->hasRole('Admin')) {
            $validated['customer_id'] = $reservation->customer_id;
        }

        $startTime = Carbon::parse($validated['reservation_time']);
        $endTime = $startTime->copy()->addHours((int) $validated['duration_hours']);

        $conflict = Reservation::where('desk_id', $validated['desk_id'])
            ->where('reservation_date', $validated['reservation_date'])
            ->where('id', '!=', $reservation->id)
            ->get()
            ->some(function ($existingReservation) use ($startTime, $endTime) {
                $existingStart = Carbon::parse($existingReservation->reservation_time);
                $existingEnd = $existingStart->copy()->addHours((int) ($existingReservation->duration_hours ?? 2));
                return $startTime->lt($existingEnd) && $endTime->gt($existingStart);
            });

        if ($conflict) {
            return back()->withErrors(['desk_id' => __('messages.desk_already_reserved')]);
        }

        $reservation->update($validated);
        $this->updateDeskStatus($reservation->desk_id, $reservation->reservation_date);
        $this->notifyCustomer($reservation->customer, 'reservation_updated');

        return redirect()->route('reservations.index')->with('success', __('messages.reservation_updated'));
    }

    public function destroy(Reservation $reservation)
    {
        $customer = $reservation->customer;
        $deskId = $reservation->desk_id;
        $date = $reservation->reservation_date;

        $reservation->delete();
        $this->updateDeskStatus($deskId, $date);
        $this->notifyCustomer($customer, 'reservation_cancelled');

        return redirect()->route('reservations.index')->with('success', __('messages.reservation_deleted'));
    }

    protected function updateDeskStatus(int $deskId, string $date)
    {
        $desk = Desk::find($deskId);
        if (!$desk) return;
    
        $now = Carbon::now();
    
        $hasOverlap = Reservation::where('desk_id', $deskId)
            ->where('reservation_date', $date)
            ->get()
            ->some(function ($reservation) use ($date, $now) {
                $start = Carbon::parse($date . ' ' . $reservation->reservation_time);
                $end = $start->copy()->addHours((int) ($reservation->duration_hours ?? 2));
                return $now->between($start, $end);
            });
    
        if ($hasOverlap) {
            if ($desk->status !== 'occupied') {
                $desk->status = 'occupied';
                $desk->save();
            }
        } elseif ($desk->status === 'occupied') {
            $desk->status = 'available';
            $desk->save();
        }
    }    

    protected function notifyCustomer(Customer $customer, string $templateKey)
    {
        $language = $customer->preferred_language ?? 'en';
        $byAdmin = auth()->user()?->hasRole('Admin') ?? false;
    
        $notification = new ReservationNotification($templateKey, $language, $byAdmin);
    
        $customer->notify($notification);
    
        if ($customer->user) {
            $customer->user->notify($notification);
        }
    }     

    public function checkConflict(Request $request)
    {
        $deskId = $request->input('desk_id');
        $date = $request->input('reservation_date');
        $time = $request->input('reservation_time');
        $duration = (int) $request->input('duration_hours', 2);

        if (!$deskId || !$date || !$time) {
            return response()->json(['conflict' => false]); // безопасно по умолчанию
        }

        $start = Carbon::parse($time);
        $end = $start->copy()->addHours($duration);

        $conflict = Reservation::where('desk_id', $deskId)
            ->where('reservation_date', $date)
            ->get()
            ->some(function ($res) use ($start, $end) {
                $resStart = Carbon::parse($res->reservation_time);
                $resEnd = $resStart->copy()->addHours((int) ($res->duration_hours ?? 2));
                return $start->lt($resEnd) && $end->gt($resStart);
            });

        return response()->json(['conflict' => $conflict]);
    }

    public function getFutureStatuses(Request $request)
    {
        $date = $request->input('reservation_date');
        $time = $request->input('reservation_time');
        $duration = (int) $request->input('duration_hours', 2);

        if (!$date || !$time) {
            return response()->json([], 400);
        }

        $start = Carbon::parse($time);
        $end = $start->copy()->addHours($duration);

        $desks = Desk::all();
        $statuses = [];

        foreach ($desks as $desk) {
            $isOccupied = Reservation::where('desk_id', $desk->id)
                ->where('reservation_date', $date)
                ->get()
                ->some(function ($res) use ($start, $end) {
                    $resStart = Carbon::parse($res->reservation_time);
                    $resEnd = $resStart->copy()->addHours((int) ($res->duration_hours ?? 2));
                    return $start->lt($resEnd) && $end->gt($resStart);
                });

            $statuses[] = [
                'id' => $desk->id,
                'status' => $isOccupied ? 'occupied' : 'available',
            ];
        }

        return response()->json($statuses);
    }


}
