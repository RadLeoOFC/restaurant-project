@extends('layouts.app')

@section('title', __('messages.browser_dashboard_title'))

@section('content')

<div style="max-height: 70px; overflow-y: auto;">
    @php
        $notifications = auth()->user()->notifications ?? [];
        $isAdmin = auth()->user()->role->role_name === 'Admin';
    @endphp

    @forelse ($notifications as $notification)
        <div style="font-size: 24px; color: green; padding-bottom: 60px;">
            {{ $notification->data['message'] }}
        </div>
    @empty
        <div style="color: gray;">{{ __('messages.no_notifications') }}</div>
    @endforelse
</div>


<div class="dashboard-container">
    <h1 style="font-size: 50px; margin-bottom:30px">
        {{ $isAdmin ? __('messages.dashboard_title_admin') : __('messages.dashboard_title_user') }}
    </h1>

    <div class="button-container">
        <a href="{{ url('/') }}" class="btn btn-primary mb-2">{{ __('messages.home') }}</a>

        @if($isAdmin)
            <a href="{{ route('roles.index') }}" class="btn btn-secondary mb-2">{{ __('messages.manage_roles') }}</a>
        @endif

        <a href="{{ route('desks.index') }}" class="btn btn-secondary mb-2">
            {{ $isAdmin ? __('messages.manage_desks_admin') : __('messages.manage_desks_user') }}
        </a>
        <a href="{{ route('desks.map') }}" class="btn btn-secondary mb-2">{{ __('messages.desk_map') }}</a>
        <a href="{{ route('reservations.index') }}" class="btn btn-success mb-2">{{ $isAdmin ? __('messages.manage_reservations_admin') : __('messages.manage_reservations_user') }}</a>
        <a href="{{ route('customers.index') }}" class="btn btn-success mb-2">{{ $isAdmin ? __('messages.manage_customers_admin') : __('messages.manage_customers_user') }}</a>
    </div>

</div>

@endsection
