<!-- resources/views/reservations/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h1 style="font-size: 30px; margin-bottom:20px">Reservation List</h1>
        <a href="{{ route('reservations.create') }}" class="btn btn-primary">Add Reservation</a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Desk</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reservations as $reservation)
                    <tr>
                        <td>{{ $reservation->desk->name }}</td>
                        <td>{{ $reservation->customer->name }}</td>
                        <td>{{ $reservation->reservation_date }}</td>
                        <td>{{ $reservation->reservation_time }}</td>
                        <td>{{ $reservation->status }}</td>
                        <td class="text-center">
                            <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('reservations.destroy', $reservation) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No reservations found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
