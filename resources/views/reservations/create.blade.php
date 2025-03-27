<!-- resources/views/reservations/create.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4" style="font-size: 30px; margin-bottom:20px">Create Reservation</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('reservations.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="desk_id" class="form-label d-block text-start">Desk</label>
                <select name="desk_id" id="desk_id" class="form-select" required>
                    @foreach ($desks as $desk)
                        <option value="{{ $desk->id }}">
                            {{ $desk->name }} ({{ $desk->status }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="customer_name" class="form-label d-block text-start">Customer Name</label>
                <input type="text" name="customer_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="contact" class="form-label d-block text-start">Contact</label>
                <input type="text" name="contact" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="reservation_date" class="form-label d-block text-start">Date</label>
                <input type="date" name="reservation_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="reservation_time" class="form-label d-block text-start">Time</label>
                <input type="time" name="reservation_time" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label d-block text-start">Status</label>
                <select name="status" class="form-select">
                    <option value="new">New</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <div>
                <a href="{{ route('reservations.index') }}" class="btn btn-secondary">Back to List</a>
                <button type="submit" class="btn btn-success">Create</button>
            </div>
        </form>
    </div>
@endsection
