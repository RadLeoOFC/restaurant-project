@extends('layouts.app')

@section('title', __('messages.edit_customer'))

@section('content')
<div class="container mt-4">
    <h2 style="font-size: 30px; margin-bottom:20px">
        {{ auth()->user()->hasRole('Admin') ? __('messages.edit_customer') : __('messages.edit_profile') }}
    </h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $user = auth()->user();
    @endphp

    <form action="{{ route('customers.update', $customer) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label d-block text-start">{{ __('messages.name') }}</label>
            <input type="text" name="name" class="form-control" value="{{ $customer->name }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label d-block text-start">{{ __('messages.email') }}</label>
            <input type="email" name="email" class="form-control" value="{{ $customer->email }}" required>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label d-block text-start">{{ __('messages.phone') }}</label>
            <input type="text" name="phone" class="form-control" value="{{ $customer->phone }}" required>
        </div>

        <div class="mb-3">
            <label for="preferred_language" class="form-label d-block text-start">{{ __('messages.preferred_language') }}</label>
            <select name="preferred_language" class="form-control">
                @foreach(\App\Models\Language::all() as $lang)
                    <option value="{{ $lang->code }}" {{ $customer->preferred_language === $lang->code ? 'selected' : '' }}>
                        {{ $lang->name }}
                    </option>
                @endforeach
            </select>
        </div>

        @if ($user->hasRole('Admin'))
            <div class="mb-3">
                <label for="user_id" class="form-label d-block text-start">{{ __('messages.select_user') }}</label>
                <select name="user_id" class="form-control">
                    <option value="">{{ __('messages.no_user_selected') }}</option>
                    @foreach(\App\Models\User::all() as $u)
                        <option value="{{ $u->id }}" {{ $customer->user_id == $u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ $u->email }})
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        <div>
            <a href="{{ route('customers.index') }}" class="btn btn-secondary">{{ __('messages.back') }}</a>
            <button type="submit" class="btn btn-success">{{ __('messages.update') }}</button>
        </div>
    </form>
</div>
@endsection
