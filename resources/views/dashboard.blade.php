@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div style="max-height: 100px; overflow: auto; margin-bottom: 20px;">
    @foreach (auth()->user()->notifications as $notification)
        <div style="font-size: 30px; color: green;">{{ $notification->data['message'] }}</div>
    @endforeach
</div>

<div class="dashboard-container">

    <h1 style="font-size: 50px; margin-bottom:30px">This is the dashboard</h1>

    <div class="button-container">
        <a href="{{ url('/') }}" class="btn btn-primary">Home</a>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Manage Roles</a>
        <a href="{{ route('desks.index') }}" class="btn btn-secondary">Manage Desks</a>
        <a href="{{ route('desks.map') }}" class="btn btn-secondary">Desk Map</a>
        <a href="{{ route('reservations.index') }}" class="btn btn-success">Manage reservations</a>
        <a href="{{ route('customers.index') }}" class="btn btn-success">Manage customers</a>
    </div>

</div>

@endsection
