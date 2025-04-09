@extends('layouts.app')

@section('title', __('messages.translations'))

@section('content')
<div class="container">
    <h1 style="font-size: 30px; margin-bottom:20px">{{ __('messages.translations') }}</h1>
    <a href="{{ route('translations.create') }}" class="btn btn-primary mb-3">{{ __('messages.add_translation') }}</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>{{ __('messages.language') }}</th>
                <th>{{ __('messages.key') }}</th>
                <th>{{ __('messages.value') }}</th>
                <th>{{ __('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($translations as $translation)
                <tr>
                    <td>{{ $translation->language->code }}</td>
                    <td>{{ $translation->key }}</td>
                    <td>{{ $translation->value }}</td>
                    <td>
                        <a href="{{ route('translations.edit', $translation) }}" class="btn btn-warning btn-sm">{{ __('messages.edit') }}</a>
                        <form action="{{ route('translations.destroy', $translation) }}" method="POST" style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">{{ __('messages.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
