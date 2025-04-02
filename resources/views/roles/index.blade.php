@extends('layouts.app')

@section('title', __('messages.roles'))

@section('content')
<div class="container mt-4">
    <h1 style="font-size: 30px; margin-bottom:20px">{{ __('messages.roles_management') }}</h1>
    <a href="{{ route('roles.create') }}" class="btn btn-primary" style="margin-bottom:10px">{{ __('messages.create_role') }}</a>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered mt-3">
        <tr>
            <th>ID</th>
            <th>{{ __('messages.role_name') }}</th>
            <th>{{ __('messages.actions') }}</th>
        </tr>
        @foreach ($roles as $role)
        <tr>
            <td class="align-middle">{{ $role->id }}</td>
            <td>{{ \App\Models\Translation::getValue('role_' . strtolower($role->role_name)) }}</td>
            <td class="text-nowrap align-middle">
                <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning btn-sm">{{ __('messages.edit') }}</a>
                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">{{ __('messages.delete') }}</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
