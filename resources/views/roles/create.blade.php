@extends('layouts.app')

@section('content')
<div class="container w-50 mx-auto mt-5">
    <h1 style="font-size: 30px; margin-bottom:20px">{{ __('messages.create_role') }}</h1>

    <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label d-block text-start">{{ __('messages.role_name') }}</label>
            <input type="text" name="role_name" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-success mb-3">{{ __('messages.create') }}</button>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary mb-3">{{ __('messages.back_to_list') }}</a>
    </form>
</div>
@endsection
