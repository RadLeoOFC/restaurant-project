@extends('layouts.app')

@section('title', __('messages.translations'))

@section('content')
<div class="container">
    <h1 style="font-size: 30px;" class="mb-4">{{ __('messages.translations') }}</h1>

    {{-- Кнопка добавления нового перевода --}}
    <a href="{{ route('translations.create') }}" class="btn btn-primary mb-3">
        {{ __('messages.add_translation') }}
    </a>

    {{-- Форма фильтрации --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('translations.index') }}">
                <div class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="{{ __('messages.search_by_key') }}" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">{{ __('messages.filter') }}</button>
                        <a href="{{ route('translations.index') }}" class="btn btn-outline-secondary">{{ __('messages.clear') }}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Таблица переводов --}}
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('messages.key') }}</th>
                <th>BG</th>
                <th>EN</th>
                <th>RU</th>
                <th>DE</th>
                <th>{{ __('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groupedTranslations as $index => $item)
                <tr>
                    <form method="POST" action="{{ route('translations.bulkUpdate') }}">
                        @csrf
                        @method('PUT')
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item['key'] }}</strong>
                            <input type="hidden" name="translations[{{ $item['key'] }}][key]" value="{{ $item['key'] }}">
                        </td>
                        @foreach(['bg', 'en', 'ru', 'de'] as $lang)
                            <td>
                                <input type="text" name="translations[{{ $item['key'] }}][{{ $lang }}]" class="form-control"
                                       value="{{ $item['translations'][$lang] ?? '' }}">
                            </td>
                        @endforeach
                        <td class="d-flex gap-1">
                            <button type="submit" class="btn btn-success btn-sm">{{ __('messages.save') }}</button>
                    </form>
                    <form action="{{ route('translations.destroyKey', ['key' => $item['key']]) }}" method="POST" onsubmit="return confirm('Delete all translations for this key?')">
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
