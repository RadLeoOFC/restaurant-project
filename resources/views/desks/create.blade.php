@extends('layouts.app')

@section('title', 'Add Desk')

@section('content')
<div class="container mt-4">

    <h1 style="font-size: 30px; margin-bottom:20px">Add Desk</h1>

    <form action="{{ route('desks.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label d-block text-start">Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label d-block text-start">Capacity</label>
            <input type="number" name="capacity" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label d-block text-start">Status</label>
            <select name="status" class="form-control">
                <option value="available">Available</option>
                <option value="occupied">Occupied</option>
                <option value="selected">Selected</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label d-block text-start">Coordinate X</label>
            <input type="number" name="coordinates_x" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label d-block text-start">Coordinate Y</label>
            <input type="number" name="coordinates_y" class="form-control" required>
        </div>
        
        <div class="button-inline">
            <a href="{{ route('desks.index') }}" class="btn btn-secondary mb-3">Back to List</a>
            <button type="submit" class="btn btn-success mb-3">Create</button>
        <div>
    
    </form>
</div>
@endsection
