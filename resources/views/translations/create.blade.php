@extends('layouts.app')

@section('title', __('messages.add_translation'))

@section('content')
<div class="container">
    <h1 style="font-size: 30px; margin-bottom:20px">{{ __('messages.add_translation') }}</h1>
    <form action="{{ route('translations.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>{{ __('messages.language') }}</label>
            <select name="language_id" class="form-control">
                @foreach($languages as $lang)
                    <option value="{{ $lang->id }}">{{ $lang->name }} ({{ $lang->code }})</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>{{ __('messages.key') }}</label>
            <input type="text" name="key" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>{{ __('messages.value') }}</label>
            <textarea name="value" class="form-control" required></textarea>
        </div>
        <a href="{{ route('translations.index') }}" class="btn btn-secondary">{{ __('messages.back') }}</a>
        <button type="submit" class="btn btn-success">{{ __('messages.create') }}</button>
    </form>
</div>
@endsection
