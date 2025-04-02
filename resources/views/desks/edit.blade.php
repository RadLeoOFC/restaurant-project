@extends('layouts.app')

@section('title', __('messages.edit_desk'))

@section('content')
<div class="container">
    <h1 style="font-size: 30px; margin-bottom:20px">{{ __('messages.edit_desk') }}</h1>

    <form action="{{ route('desks.update', $desk) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label d-block text-start">{{ __('messages.name') }}</label>
            <input type="text" name="name" class="form-control" value="{{ $desk->name }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label d-block text-start">{{ __('messages.capacity') }}</label>
            <input type="number" name="capacity" class="form-control" value="{{ $desk->capacity }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label d-block text-start">{{ __('messages.status') }}</label>
            <select name="status" class="form-control">
                <option value="available" {{ $desk->status == 'available' ? 'selected' : '' }}>{{ __('messages.status_available') }}</option>
                <option value="occupied" {{ $desk->status == 'occupied' ? 'selected' : '' }}>{{ __('messages.status_occupied') }}</option>
                <option value="selected" {{ $desk->status == 'selected' ? 'selected' : '' }}>{{ __('messages.status_selected') }}</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label d-block text-start">{{ __('messages.coordinate_x') }}</label>
            <input type="number" name="coordinates_x" class="form-control" value="{{ $desk->coordinates_x }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label d-block text-start">{{ __('messages.coordinate_y') }}</label>
            <input type="number" name="coordinates_y" class="form-control" value="{{ $desk->coordinates_y }}" required>
        </div>

        <button type="submit" class="btn btn-success mb-3">{{ __('messages.update') }}</button>
        <a href="{{ route('desks.index') }}" class="btn btn-secondary mb-3">{{ __('messages.back_to_list') }}</a>
    </form>
</div>
@endsection
