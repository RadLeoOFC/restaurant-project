@extends('layouts.app')

@section('title', __('messages.add_desk'))

@section('content')
<div class="container mt-4">

    <h1 style="font-size: 30px; margin-bottom:20px">{{ __('messages.add_desk') }}</h1>

    <form action="{{ route('desks.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label d-block text-start">{{ __('messages.name') }}</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label d-block text-start">{{ __('messages.capacity') }}</label>
            <input type="number" name="capacity" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label d-block text-start">{{ __('messages.status') }}</label>
            <select name="status" class="form-control">
                <option value="available">{{ __('messages.status_available') }}</option>
                <option value="occupied">{{ __('messages.status_occupied') }}</option>
                <option value="selected">{{ __('messages.status_selected') }}</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label d-block text-start">{{ __('messages.coordinate_x') }}</label>
            <input type="number" name="coordinates_x" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label d-block text-start">{{ __('messages.coordinate_y') }}</label>
            <input type="number" name="coordinates_y" class="form-control" required>
        </div>
        
        <div class="button-inline">
            <a href="{{ route('desks.index') }}" class="btn btn-secondary mb-3">{{ __('messages.back_to_list') }}</a>
            <button type="submit" class="btn btn-success mb-3">{{ __('messages.create') }}</button>
        </div>
    
    </form>
</div>
@endsection
