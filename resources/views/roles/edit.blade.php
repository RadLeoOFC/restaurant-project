@extends('layouts.app')

@section('content')
<div class="container">
    <h1 style="font-size: 30px; margin-bottom:20px">Edit Role</h1>
    <form action="{{ route('roles.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Role Name</label>
            <input type="text" name="role_name" class="form-control" value="{{ $role->role_name }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
