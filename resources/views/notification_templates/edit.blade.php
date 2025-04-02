@extends('layouts.app')

@section('title', __('messages.edit_notification_template'))

@section('content')
<div class="container">
    <h1 style="font-size: 30px; margin-bottom:20px">{{ __('messages.edit_notification_template') }}</h1>

    <form action="{{ route('notification-templates.update', $notificationTemplate) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="key">{{ __('messages.template_key') }}</label>
            <input type="text" name="key" class="form-control" value="{{ old('key', $notificationTemplate->key) }}" required>
        </div>

        <div class="form-group">
            <label for="language_code">{{ __('messages.language_code') }}</label>
            <input type="text" name="language_code" class="form-control" value="{{ old('language_code', $notificationTemplate->language_code) }}" required>
        </div>

        <div class="form-group">
            <label for="title">{{ __('messages.title_optional') }}</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $notificationTemplate->title) }}">
        </div>

        <div class="form-group">
            <label for="body">{{ __('messages.body_text') }}</label>
            <textarea name="body" class="form-control" rows="4" required>{{ old('body', $notificationTemplate->body) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary mt-3">{{ __('messages.update') }}</button>
        <a href="{{ route('notification-templates.index') }}" class="btn btn-secondary mt-3">{{ __('messages.back_to_list') }}</a>
    </form>
</div>
@endsection
