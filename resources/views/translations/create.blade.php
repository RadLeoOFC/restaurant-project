@extends('layouts.app')

@section('content')
<div class="container">
    <h1 style="font-size: 30px; margin-bottom:20px">Add Translation</h1>
    <form action="{{ route('translations.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Language</label>
            <select name="language_id" class="form-control">
                @foreach($languages as $lang)
                    <option value="{{ $lang->id }}">{{ $lang->name }} ({{ $lang->code }})</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Key</label>
            <input type="text" name="key" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Value</label>
            <textarea name="value" class="form-control" required></textarea>
        </div>
        <a href="{{ route('translations.index') }}" class="btn btn-secondary">Back</a>
        <button type="submit" class="btn btn-success">Create</button>
    </form>
</div>
@endsection
