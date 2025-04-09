@extends('layouts.app')

@section('title', __('messages.edit_reservation'))

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4" style="font-size: 30px; margin-bottom:20px">{{ __('messages.edit_reservation') }}</h2>

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
                <label for="desk_id" class="form-label d-block text-start">{{ __('messages.desk') }}</label>
                <select name="desk_id" id="desk_id" class="form-select" required>
                    @foreach ($desks as $desk)
                        <option value="{{ $desk->id }}" {{ $reservation->desk_id == $desk->id ? 'selected' : '' }}>
                            {{ $desk->name }}  ({{ $desk->status }})
                        </option>
                    @endforeach
                </select>
            </div>

            @php $user = auth()->user(); @endphp

            @if ($user->hasRole('Admin'))
                <div class="mb-3">
                    <label for="customer_id" class="form-label d-block text-start">{{ __('messages.customer') }}</label>
                    <select name="customer_id" id="customer_id" class="form-select" required>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" {{ $reservation->customer_id == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="mb-3">
                <label for="reservation_date" class="form-label d-block text-start">{{ __('messages.date') }}</label>
                <input type="date" name="reservation_date" class="form-control" value="{{ $reservation->reservation_date }}" required>
            </div>

            <div class="mb-3">
                <label for="reservation_time" class="form-label d-block text-start">{{ __('messages.time') }}</label>
                <input type="time" name="reservation_time" class="form-control" value="{{ $reservation->reservation_time }}" required>
            </div>

            <div class="mb-3">
                <label for="duration_hours" class="form-label d-block text-start">{{ __('messages.duration') }}</label>
                <select name="duration_hours" id="duration_hours" class="form-select" required>
                    @for ($i = 2; $i <= 8; $i++)
                        <option value="{{ $i }}" {{ old('duration_hours', 4) == $i ? 'selected' : '' }}>
                            {{ $i }} {{ __('messages.hours') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label d-block text-start">{{ __('messages.status') }}</label>
                <select name="status" class="form-select">
                    <option value="new" {{ $reservation->status === 'new' ? 'selected' : '' }}>{{ __('messages.status_new') }}</option>
                    <option value="confirmed" {{ $reservation->status === 'confirmed' ? 'selected' : '' }}>{{ __('messages.status_confirmed') }}</option>
                    <option value="cancelled" {{ $reservation->status === 'cancelled' ? 'selected' : '' }}>{{ __('messages.status_cancelled') }}</option>
                </select>
            </div>

            <div>
                <a href="{{ route('reservations.index') }}" class="btn btn-secondary">{{ __('messages.back_to_list') }}</a>
                <button type="submit" class="btn btn-success">{{ __('messages.update') }}</button>
            </div>
        </form>
    </div>
@endsection
