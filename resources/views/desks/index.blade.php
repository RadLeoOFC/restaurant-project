@extends('layouts.app')

@section('title', 'Desks')

@section('content')
<div class="container mt-4">
    <h1 style="font-size: 30px; margin-bottom:20px">Desk List</h1>
    <a href="{{ route('desks.create') }}" class="btn btn-primary mb-3" style="margin-bottom:10px">Add Desk</a>
    <a href="{{ route('desks.map') }}" class="btn btn-secondary mb-3">View Map</a>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Name</th>
                <th>Capacity</th>
                <th>Status</th>
                <th>Coordinates</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($desks as $desk)
                <tr>
                    <td class="align-middle">{{ $desk->name }}</td>
                    <td class="align-middle">{{ $desk->capacity }}</td>
                    <td class="align-middle">{{ $desk->status }}</td>
                    <td class="align-middle">({{ $desk->coordinates_x }}, {{ $desk->coordinates_y }})</td>
                    <td class="text-nowrap align-middle">
                        <a href="{{ route('desks.edit', $desk) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('desks.destroy', $desk) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
