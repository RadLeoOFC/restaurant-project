@extends('layouts.app')

@section('title', 'Create Notification Template')

@section('content')
<div class="container">
    <h1 style="font-size: 30px; margin-bottom:20px">Create Notification Template</h1>

    <form action="{{ route('notification-templates.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="key" class="form-label d-block text-start">Template Key</label>
            <input type="text" name="key" class="form-control" value="{{ old('key') }}" required>
        </div>

        <div class="form-group">
            <label for="language_code" class="form-label d-block text-start">Language Code (e.g. en, ru)</label>
            <input type="text" name="language_code" class="form-control" value="{{ old('language_code') }}" required>
        </div>

        <div class="form-group">
            <label for="title" class="form-label d-block text-start">Title (optional)</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}">
        </div>

        <div class="form-group">
            <label for="body" class="form-label d-block text-start">Body Text</label>
            <textarea name="body" class="form-control" rows="4" required>{{ old('body') }}</textarea>
        </div>

        <button type="submit" class="btn btn-success mt-3">Create</button>
        <a href="{{ route('notification-templates.index') }}" class="btn btn-secondary mt-3">Back to list</a>
    </form>
</div>
@endsection
