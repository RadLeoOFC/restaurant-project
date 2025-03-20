@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-container">
    <h1 style="font-size: 50px; margin-bottom:30px">This is the dashboard</h1>

    <div class="button-container">
        <a href="{{ url('/') }}" class="btn btn-primary">Home</a>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Manage Roles</a>
        <a href="{{ route('desks.index') }}" class="btn btn-success">Manage Desks</a>
    </div>
</div>
@endsection
