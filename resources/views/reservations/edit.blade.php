<!-- resources/views/reservations/edit.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4" style="font-size: 30px; margin-bottom:20px">Edit Reservation</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('reservations.update', $reservation) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="desk_id" class="form-label d-block text-start">Desk</label>
                <select name="desk_id" id="desk_id" class="form-select" required>
                    @foreach ($desks as $desk)
                        <option value="{{ $desk->id }}" {{ $reservation->desk_id == $desk->id ? 'selected' : '' }}>
                            {{ $desk->name }}  ({{ $desk->status }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="customer_name" class="form-label d-block text-start">Customer Name</label>
                <input type="text" name="customer_name" class="form-control" value="{{ $reservation->customer_name }}" required>
            </div>

            <div class="mb-3">
                <label for="contact" class="form-label d-block text-start">Contact</label>
                <input type="text" name="contact" class="form-control" value="{{ $reservation->contact }}" required>
            </div>

            <div class="mb-3">
                <label for="reservation_date" class="form-label d-block text-start">Date</label>
                <input type="date" name="reservation_date" class="form-control" value="{{ $reservation->reservation_date }}" required>
            </div>

            <div class="mb-3">
                <label for="reservation_time" class="form-label d-block text-start">Time</label>
                <input type="time" name="reservation_time" class="form-control" value="{{ $reservation->reservation_time }}" required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label d-block text-start">Status</label>
                <select name="status" class="form-select">
                    <option value="new" {{ $reservation->status === 'new' ? 'selected' : '' }}>New</option>
                    <option value="confirmed" {{ $reservation->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="cancelled" {{ $reservation->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div>
                <a href="{{ route('reservations.index') }}" class="btn btn-secondary">Back to List</a>
                <button type="submit" class="btn btn-success">Update</button>
            </div>
        </form>
    </div>
@endsection
