@extends('layouts.app')

@section('content')
<div class="home-container">
    <h1 style="font-size: 45px; margin-bottom:30px">{{ __('messages.welcome_title') }}</h1>
    <p style="margin-bottom:70px">{{ __('messages.welcome_description') }}</p>

    <div class="button-container">
        <a href="{{ url('/dashboard') }}" class="btn btn-primary mb-3">{{ __('messages.go_to_dashboard') }}</a>
        <a href="{{ url('/register') }}" class="btn btn-secondary mb-3">{{ __('messages.register') }}</a> 
        <a href="{{ url('/login') }}" class="btn btn-secondary mb-3">{{ __('messages.login') }}</a>
    </div>
</div>
@endsection
