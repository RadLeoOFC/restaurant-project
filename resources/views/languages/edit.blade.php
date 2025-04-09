@extends('layouts.app')

@section('title', __('messages.edit_language'))

@section('content')
    <div class="container">
        <h1 style="font-size: 30px; margin-bottom:20px">{{ __('messages.edit_language') }}</h1>

        <form action="{{ route('languages.update', $language) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="code" class="form-label d-block text-start">{{ __('messages.code') }}</label>
                <input type="text" name="code" value="{{ $language->code }}" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label d-block text-start">{{ __('messages.name') }}</label>
                <input type="text" name="name" value="{{ $language->name }}" class="form-control" required>
            </div>
            <button class="btn btn-primary">{{ __('messages.update') }}</button>
            <a href="{{ route('languages.index') }}" class="btn btn-secondary">{{ __('messages.back_to_list') }}</a>
        </form>
    </div>
@endsection
