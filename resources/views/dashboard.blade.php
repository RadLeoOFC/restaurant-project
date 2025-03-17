@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1>This is the dashboard</h1>
    <a href="{{ url('/') }}" class="btn btn-outline-primary mb-3">Go to home</a>
@endsection
