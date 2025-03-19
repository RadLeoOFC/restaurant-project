@extends('layouts.app')

@section('title', 'Roles')

@section('content')
<div class="container mt-4">
    <h1 style="font-size: 30px; margin-bottom:20px">Roles Management</h1>
    <a href="{{ route('roles.create') }}" class="btn btn-primary" style="margin-bottom:10px">Create New Role</a>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered mt-3">
        <tr>
            <th>ID</th>
            <th>Role Name</th>
            <th>Actions</th>
        </tr>
        @foreach ($roles as $role)
        <tr>
            <td class="align-middle">{{ $role->id }}</td>
            <td class="align-middle">{{ $role->role_name }}</td>
            <td class="text-nowrap align-middle">
                <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning btn-sm">Edit</a>
                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>

</div>
@endsection
