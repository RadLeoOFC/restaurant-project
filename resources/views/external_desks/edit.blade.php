
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 style="font-size: 30px; margin-bottom:20px">{{ __('messages.edit_external_desk') }}</h1>
    <form method="POST" action="{{ route('external-desks.update', $externalDesk) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label d-block text-start">{{ __('messages.name') }}</label>
            <input type="text" name="name" value="{{ $externalDesk->name }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label d-block text-start">{{ __('messages.capacity') }}</label>
            <input type="number" name="capacity" value="{{ $externalDesk->capacity }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label d-block text-start">{{ __('messages.status') }}</label>
            <select name="status" class="form-control" required>
                <option value="available" @if($externalDesk->status == 'available') selected @endif>{{ __('messages.status_available') }}</option>
                <option value="occupied" @if($externalDesk->status == 'occupied') selected @endif>{{ __('messages.status_occupied') }}</option>
                <option value="selected" @if($externalDesk->status == 'selected') selected @endif>{{ __('messages.status_selected') }}</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label d-block text-start">{{ __('messages.coordinate_x') }}</label>
            <input type="number" step="0.1" name="coordinates_x" value="{{ $externalDesk->coordinates_x }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label d-block text-start">{{ __('messages.coordinate_y') }}</label>
            <input type="number" step="0.1" name="coordinates_y" value="{{ $externalDesk->coordinates_y }}" class="form-control" required>
        </div>
        <a href="{{ route('external-desks.index') }}" class="btn btn-secondary">{{ __('messages.back_to_list') }}</a>
        <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
    </form>
</div>
@endsection
