@extends('layouts.app')

@section('title', __('messages.browser_dashboard_title'))

@section('content')

<div style="max-height: 50px; overflow: auto; margin-bottom: 20px;">
    @php
        $notifications = auth()->user()->notifications ?? [];
    @endphp

    @forelse ($notifications as $notification)
        <div style="font-size: 25px; color: green; margin-bottom: 10px;">
            {{ $notification->data['message'] }}
        </div>
    @empty
        <div style="color: gray;">{{ __('messages.no_notifications') }}</div>
    @endforelse
</div>

<div class="dashboard-container">

    <h1 style="font-size: 50px; margin-bottom:30px">{{ __('messages.dashboard_title') }}</h1>

    <div class="button-container">
        <a href="{{ url('/') }}" class="btn btn-primary">{{ __('messages.home') }}</a>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">{{ __('messages.manage_roles') }}</a>
        <a href="{{ route('desks.index') }}" class="btn btn-secondary">{{ __('messages.manage_desks') }}</a>
        <a href="{{ route('desks.map') }}" class="btn btn-secondary">{{ __('messages.desk_map') }}</a>
        <a href="{{ route('reservations.index') }}" class="btn btn-success">{{ __('messages.manage_reservations') }}</a>
        <a href="{{ route('customers.index') }}" class="btn btn-success">{{ __('messages.manage_customers') }}</a>
    </div>

</div>

@endsection
