@extends('layouts.app')

@section('content')
<div class="container">
    <h1 style="font-size: 30px; margin-bottom:20px">Edit Translation</h1>

    <form action="{{ route('translations.update', $translation) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="language_id" class="form-label">Language</label>
            <select name="language_id" id="language_id" class="form-control">
                @foreach($languages as $language)
                    <option value="{{ $language->id }}" {{ $translation->language_id == $language->id ? 'selected' : '' }}>
                        {{ $language->name }} ({{ $language->code }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="key" class="form-label">Key</label>
            <input type="text" name="key" class="form-control" value="{{ $translation->key }}" required>
        </div>

        <div class="mb-3">
            <label for="value" class="form-label">Value</label>
            <textarea name="value" class="form-control" rows="4" required>{{ $translation->value }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('translations.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
