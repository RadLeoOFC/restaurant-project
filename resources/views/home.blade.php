@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="home-container">
    <h1>Welcome to the restaurant management system!</h1>
    <p>Manage reservations, desks, and more with ease.</p>

    <div class="button-container">
        <a href="{{ url('/dashboard') }}" class="btn btn-primary mb-3">Go to Dashboard</a>
        <a href="{{ url('/register') }}" class="btn btn-secondary mb-3">Register</a> 
        <a href="{{ url('/login') }}" class="btn btn-outline-primary mb-3">Login</a>
    </div>
</div>
@endsection
