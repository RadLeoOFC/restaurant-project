@extends('layouts.app')

@section('title', __('messages.reservation_list'))

@section('content')
    <div class="container mt-4">
        <h1 style="font-size: 30px; margin-bottom:20px">{{ __('messages.reservation_list') }}</h1>
        <a href="{{ route('reservations.create') }}" class="btn btn-primary">{{ __('messages.add_reservation') }}</a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>{{ __('messages.desk') }}</th>
                    <th>{{ __('messages.customer') }}</th>
                    <th>{{ __('messages.date') }}</th>
                    <th>{{ __('messages.time') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.period') }}</th>
                    <th class="text-center">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reservations as $reservation)
                    <tr>
                        <td>{{ $reservation->desk->translated_name }}</td>
                        <td>{{ $reservation->customer->name }}</td>
                        <td>{{ $reservation->reservation_date }}</td>
                        <td>{{ $reservation->reservation_time }}</td>
                        <td>{{ __('messages.status_' . $reservation->status) }}</td>
                        <td>{{ $reservation->duration_hours }}</td>
                        <td class="text-center">
                            <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-sm btn-warning">{{ __('messages.edit') }}</a>
                            <form action="{{ route('reservations.destroy', $reservation) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('messages.are_you_sure') }}')">
                                    {{ __('messages.delete') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">{{ __('messages.no_reservations_found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
