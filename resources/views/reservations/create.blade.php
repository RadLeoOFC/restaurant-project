@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4" style="font-size: 30px; margin-bottom:20px">{{ __('messages.add_reservation') }}</h2>

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
                <label for="desk_id" class="form-label d-block text-start">{{ __('messages.desk') }}</label>
                <select name="desk_id" id="desk_id" class="form-select" required>
                    @foreach ($desks as $desk)
                        <option value="{{ $desk->id }}">
                            {{ $desk->name }} ({{ $desk->status }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="customer_id" class="form-label d-block text-start">{{ __('messages.customer') }}</label>
                <select name="customer_id" id="customer_id" class="form-select" required>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="reservation_date" class="form-label d-block text-start">{{ __('messages.date') }}</label>
                <input type="date" name="reservation_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="reservation_time" class="form-label d-block text-start">{{ __('messages.time') }}</label>
                <input type="time" name="reservation_time" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label d-block text-start">{{ __('messages.status') }}</label>
                <select name="status" class="form-select">
                    <option value="new">{{ __('messages.status_new') }}</option>
                    <option value="confirmed">{{ __('messages.status_confirmed') }}</option>
                    <option value="cancelled">{{ __('messages.status_cancelled') }}</option>
                </select>
            </div>

            <div>
                <a href="{{ route('reservations.index') }}" class="btn btn-secondary">{{ __('messages.back_to_list') }}</a>
                <button type="submit" class="btn btn-success">{{ __('messages.create') }}</button>
            </div>
        </form>
    </div>
@endsection
