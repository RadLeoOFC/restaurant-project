@extends('layouts.app')

@section('content')
<div class="container w-50 mx-auto mt-5">
    <h1 style="font-size: 30px; margin-bottom:20px">Create New Role</h1>

    <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label d-block text-start">Role Name</label>
            <input type="text" name="role_name" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-success mb-3">Create</button>

        <a href="{{ route('roles.index') }}" class="btn btn-secondary mb-3">Back to List</a>
    </form>
</div>
@endsection
