@extends('layouts.app')

@section('title', 'Edit Desk')

@section('content')
<div class="container">
    <h1 style="font-size: 30px; margin-bottom:20px">Edit Desk</h1>

    <form action="{{ route('desks.update', $desk) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label form-label d-block text-start">Name</label>
            <input type="text" name="name" class="form-control" value="{{ $desk->name }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label d-block text-start">Capacity</label>
            <input type="number" name="capacity" class="form-control" value="{{ $desk->capacity }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label d-block text-start">Status</label>
            <select name="status" class="form-control">
                <option value="available" {{ $desk->status == 'available' ? 'selected' : '' }}>Available</option>
                <option value="occupied" {{ $desk->status == 'occupied' ? 'selected' : '' }}>Occupied</option>
                <option value="selected" {{ $desk->status == 'selected' ? 'selected' : '' }}>Selected</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label d-block text-start">Coordinate X</label>
            <input type="number" name="coordinates_x" class="form-control" value="{{ $desk->coordinates_x }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label d-block text-start">Coordinate Y</label>
            <input type="number" name="coordinates_y" class="form-control" value="{{ $desk->coordinates_y }}" required>
        </div>

        <button type="submit" class="btn btn-success mb-3">Update</button>
        <a href="{{ route('desks.index') }}" class="btn btn-secondary mb-3">Back to List</a>
    </form>
</div>
@endsection
